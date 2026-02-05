<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\StockDetail;
use App\Services\InventoryService;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    protected $inventoryService;
    protected $accountingService;

    public function __construct(InventoryService $inventoryService, AccountingService $accountingService)
    {
        $this->inventoryService = $inventoryService;
        $this->accountingService = $accountingService;
    }

    /**
     * Display a listing of purchase orders.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'items.product'])->latest()->paginate(15);
        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created purchase order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expected_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'order_number' => 'PO-' . time(),
                'expected_date' => $request->expected_date,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order created successfully.');
    }

    /**
     * Display the specified purchase order.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified purchase order.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $purchaseOrder->load('items');
        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    /**
     * Update the specified purchase order.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expected_date' => 'required|date',
            'status' => 'required|in:pending,approved,received,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'expected_date' => $request->expected_date,
                'total_amount' => $totalAmount,
                'status' => $request->status,
            ]);

            // Delete existing items and create new ones
            $purchaseOrder->items()->delete();
            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order updated successfully.');
    }

    /**
     * Receive stock for a purchase order.
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'received_date' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {
            $totalReceivedValue = 0;

            foreach ($purchaseOrder->items as $orderItem) {
                $remainingQuantity = $orderItem->quantity - ($orderItem->received_quantity ?? 0);

                if ($remainingQuantity > 0) {
                    // Update or create product inventory
                    $productInventory = ProductInventory::where('product_id', $orderItem->product_id)->first();

                    if ($productInventory) {
                        // Update existing inventory
                        $productInventory->quantity += $remainingQuantity;
                        $productInventory->buy_price = $orderItem->unit_price; // Update cost
                        $productInventory->save();
                    } else {
                        // Create new inventory record
                        ProductInventory::create([
                            'product_id' => $orderItem->product_id,
                            'quantity' => $remainingQuantity,
                            'buy_price' => $orderItem->unit_price,
                            'price' => $orderItem->unit_price * 1.5, // Default markup
                        ]);
                    }

                    // Update purchase order item
                    $orderItem->received_quantity = $orderItem->quantity; // Mark as fully received
                    $orderItem->save();

                    // Record inventory movement
                    \App\Models\InventoryMovement::create([
                        'product_id' => $orderItem->product_id,
                        'movement_type' => 'IN',
                        'quantity' => $remainingQuantity,
                        'unit_cost' => $orderItem->unit_price,
                        'total_value' => $remainingQuantity * $orderItem->unit_price,
                        'reference_type' => 'App\Models\PurchaseOrder',
                        'reference_id' => $purchaseOrder->id,
                        'notes' => 'Stock received from purchase order',
                        'user_id' => auth()->id(),
                    ]);

                    $totalReceivedValue += $remainingQuantity * $orderItem->unit_price;
                }
            }

            // Record accounting entry for purchase
            if ($totalReceivedValue > 0) {
                $this->accountingService->recordPurchase($purchaseOrder);
            }

            // Update purchase order status
            $purchaseOrder->update([
                'status' => 'received',
                'received_date' => $request->received_date,
            ]);
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Stock received successfully.');
    }

    /**
     * Remove the specified purchase order.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return redirect()->back()->with('error', 'Cannot delete a received purchase order.');
        }

        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order deleted successfully.');
    }
}
