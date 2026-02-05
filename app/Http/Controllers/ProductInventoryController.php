<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\Size;
use App\Models\Color;
use App\Models\InventoryMovement;
use App\Models\StockDetail;
use Illuminate\Http\Request;

class ProductInventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $inventories = ProductInventory::with('product', 'size', 'color')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('product_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('size', function ($q) use ($search) {
                    $q->where('size_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('color', function ($q) use ($search) {
                    $q->where('color_name', 'like', '%' . $search . '%');
                });
            })
            ->paginate(20); // Paginate to prevent loading too many records
        return view('Backend.inventory.index', compact('inventories', 'search'));
    }

    public function erpIndex()
    {
        $totalProducts = Product::count();
        $totalStock = ProductInventory::sum('quantity');
        $lowStockCount = ProductInventory::where('quantity', '<=', 10)
            ->where('quantity', '>', 0)
            ->count();
        $outOfStockCount = ProductInventory::where('quantity', '=', 0)->count();

        // Expiry alerts
        $expiredCount = ProductInventory::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now())
            ->where('quantity', '>', 0)
            ->count();

        $expiringSoonCount = ProductInventory::whereNotNull('expiry_date')
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('quantity', '>', 0)
            ->count();

        $inventory = ProductInventory::with('product', 'size', 'color')
            ->get()
            ->map(function ($item) {
                $product = $item->product;
                $status = $item->quantity == 0 ? 'out_of_stock' : ($item->quantity <= 10 ? 'low_stock' : 'in_stock');
                $expiryStatus = $item->getExpiryStatus();
                $daysUntilExpiry = $item->getDaysUntilExpiry();

                return (object) [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $product->product_name ?? 'Unknown',
                    'sku' => $product->sku ?? 'N/A',
                    'size' => $item->size->size_name ?? 'N/A',
                    'color' => $item->color->color_name ?? 'N/A',
                    'current_stock' => $item->quantity,
                    'reserved_stock' => 0,
                    'available_stock' => $item->quantity,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'status' => $status,
                    'expiry_status' => $expiryStatus,
                    'expiry_date' => $item->expiry_date?->format('Y-m-d'),
                    'days_until_expiry' => $daysUntilExpiry,
                    'manufacture_date' => $item->manufacture_date?->format('Y-m-d'),
                    'batch_number' => $item->batch_number,
                ];
            });

        $productIds = ProductInventory::pluck('product_id')->unique();
        $recentMovements = InventoryMovement::with('product')
            ->whereIn('product_id', $productIds)
            ->latest()
            ->take(10)
            ->get();

        return view('inventory.index', compact('totalProducts', 'totalStock', 'lowStockCount', 'outOfStockCount', 'expiredCount', 'expiringSoonCount', 'inventory', 'recentMovements'));
    }

    public function create()
    {
        $products = Product::all();
        $sizes = Size::all();
        $colors = Color::all();
        return view('Backend.inventory.create', compact('products', 'sizes', 'colors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size_id' => 'nullable|exists:sizes,id',
            'color_id' => 'nullable|exists:colors,id',
            'buy_price' => 'nullable|numeric',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'reseller_price' => 'nullable|numeric',
            'wholesale_price' => 'nullable|numeric',
            'distributer_price' => 'nullable|numeric',
            'quantity' => 'required|integer|min:0',
            'weight_grams' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'manufacture_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:255',
        ]);

        $inventory = ProductInventory::create($request->all());

        // Create inventory movement
        if ($request->quantity > 0) {
            InventoryMovement::create([
                'product_id' => $request->product_id,
                'movement_type' => 'IN',
                'quantity' => $request->quantity,
                'unit_cost' => $request->buy_price,
                'total_value' => $request->quantity * ($request->buy_price ?? 0),
                'reference_type' => 'inventory_add',
                'reference_id' => $inventory->id,
                'reason' => 'Inventory added via backend',
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('product-inventory.index')->with('success', 'Inventory added successfully');
    }

    public function edit(ProductInventory $inventory)
    {
        $products = Product::all();
        $sizes = Size::all();
        $colors = Color::all();
        return view('Backend.inventory.edit', compact('inventory', 'products', 'sizes', 'colors'));
    }

    public function update(Request $request, ProductInventory $inventory)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size_id' => 'nullable|exists:sizes,id',
            'color_id' => 'nullable|exists:colors,id',
            'buy_price' => 'nullable|numeric',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'reseller_price' => 'nullable|numeric',
            'wholesale_price' => 'nullable|numeric',
            'distributer_price' => 'nullable|numeric',
            'amazon_price' => 'nullable|numeric',
            'quantity' => 'required|integer|min:0',
            'weight_grams' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date|after:today',
            'manufacture_date' => 'nullable|date|before_or_equal:today',
            'batch_number' => 'nullable|string|max:255',
        ]);

        $oldQuantity = $inventory->quantity;
        $inventory->update($request->all());
        $newQuantity = $request->quantity;

        // Create inventory movement if quantity changed
        $quantityDifference = $newQuantity - $oldQuantity;
        if ($quantityDifference != 0) {
            InventoryMovement::create([
                'product_id' => $request->product_id,
                'movement_type' => $quantityDifference > 0 ? 'IN' : 'OUT',
                'quantity' => abs($quantityDifference),
                'unit_cost' => $request->buy_price,
                'total_value' => abs($quantityDifference) * ($request->buy_price ?? 0),
                'reference_type' => 'inventory_update',
                'reference_id' => $inventory->id,
                'reason' => 'Inventory updated via backend',
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('product-inventory.index')->with('success', 'Inventory updated successfully');
    }

    public function destroy(ProductInventory $inventory)
    {
        // Create inventory movement for the quantity being removed
        if ($inventory->quantity > 0) {
            InventoryMovement::create([
                'product_id' => $inventory->product_id,
                'movement_type' => 'OUT',
                'quantity' => $inventory->quantity,
                'unit_cost' => $inventory->buy_price,
                'total_value' => $inventory->quantity * ($inventory->buy_price ?? 0),
                'reference_type' => 'inventory_delete',
                'reference_id' => $inventory->id,
                'reason' => 'Inventory deleted via backend',
                'user_id' => auth()->id(),
            ]);
        }

        $inventory->delete();
        return redirect()->route('product-inventory.index')->with('success', 'Inventory deleted successfully');
    }

    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q');
        $suggestions = [];

        if ($query) {
            $products = Product::where('product_name', 'like', '%' . $query . '%')->pluck('product_name')->toArray();
            $sizes = Size::where('size_name', 'like', '%' . $query . '%')->pluck('size_name')->toArray();
            $colors = Color::where('color_name', 'like', '%' . $query . '%')->pluck('color_name')->toArray();

            $suggestions = array_unique(array_merge($products, $sizes, $colors));
        }

        return response()->json($suggestions);
    }

    public function productSuggestions(Request $request)
    {
        $query = $request->get('q');
        $suggestions = [];

        if ($query) {
            $products = Product::where('product_name', 'like', '%' . $query . '%')->get(['id', 'product_name']);
            $suggestions = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->product_name
                ];
            });
        }

        return response()->json(['results' => $suggestions]);
    }

   function color(){
        $colors=Color::all();
        return view('backend.inventory.color.color',compact('colors'));
    }
    function size(){
        $sizes=Size::all();
        return view('backend.inventory.size.size',compact('sizes'));
    }

    function color_add (Request $request ){
       Color::insert([
       'color_name'=>$request->color_name,
       'color_code'=>$request->color_code,
       ]);
       return back();
    }
    function size_add (Request $request ){
       Size::insert([
       'size_name'=>$request->size_name,
       ]);
       return back();
    }

    function color_delete($id){
        Color::find($id)->delete();
        return back();
    }
    function size_delete($id){
        Size::find($id)->delete();
        return back();
    }

    /**
     * Adjust stock for a product
     */
    public function adjustStock(Request $request, $productId)
    {
        $request->validate([
            'type' => 'required|in:ADJUSTMENT,DAMAGE,MISSING',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $product = \App\Models\Product::findOrFail($productId);

        try {
            if ($request->type === 'ADJUSTMENT') {
                // Add stock - find or create ProductInventory entry
                $inventory = ProductInventory::where('product_id', $productId)->first();

                if (!$inventory) {
                    // Create new inventory entry
                    ProductInventory::create([
                        'product_id' => $productId,
                        'size_id' => null,
                        'color_id' => null,
                        'buy_price' => 0,
                        'price' => 0,
                        'discount_price' => null,
                        'quantity' => $request->quantity,
                    ]);
                } else {
                    // Update existing inventory
                    $inventory->update(['quantity' => $inventory->quantity + $request->quantity]);
                }

                // Log inventory movement
                \App\Models\InventoryMovement::create([
                    'product_id' => $productId,
                    'movement_type' => 'IN',
                    'quantity' => $request->quantity,
                    'unit_cost' => 0,
                    'total_value' => 0,
                    'reference_type' => 'stock_adjustment',
                    'reference_id' => $productId,
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                    'user_id' => auth()->id(),
                ]);
            } else {
                // Subtract stock - use direct ProductInventory update
                $inventory = ProductInventory::where('product_id', $productId)->first();

                if (!$inventory) {
                    throw new \Exception('No inventory found for this product');
                }

                if ($inventory->quantity < $request->quantity) {
                    throw new \Exception('Insufficient stock available');
                }

                // Update inventory quantity
                $inventory->update(['quantity' => $inventory->quantity - $request->quantity]);

                // Log inventory movement
                \App\Models\InventoryMovement::create([
                    'product_id' => $productId,
                    'movement_type' => 'OUT',
                    'quantity' => $request->quantity,
                    'unit_cost' => $inventory->buy_price ?? 0,
                    'total_value' => $request->quantity * ($inventory->buy_price ?? 0),
                    'reference_type' => 'stock_adjustment',
                    'reference_id' => $productId,
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                    'user_id' => auth()->id(),
                ]);
            }

            return redirect()->back()->with('success', 'Stock adjusted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to adjust stock: ' . $e->getMessage());
        }
    }

    // API Methods for ERP Inventory
    public function getStockStatus()
    {
        $inventory = ProductInventory::with('product')
            ->get()
            ->map(function ($item) {
                $product = $item->product;
                $status = $item->quantity == 0 ? 'out_of_stock' : ($item->quantity <= 10 ? 'low_stock' : 'in_stock');

                return [
                    'product_id' => $item->product_id,
                    'product_name' => $product->product_name ?? 'Unknown',
                    'sku' => $product->sku ?? 'N/A',
                    'brand' => $product->brand->name ?? 'N/A',
                    'current_stock' => $item->quantity,
                    'status' => $status,
                ];
            });

        return response()->json($inventory);
    }

    public function getStockDetails($productId)
    {
        $inventoryItems = ProductInventory::with('product', 'size', 'color')
            ->where('product_id', $productId)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'batch_number' => $item->batch_number,
                    'manufacture_date' => $item->manufacture_date?->format('Y-m-d'),
                    'purchase_price' => $item->buy_price ?? 0,
                    'quantity' => $item->quantity,
                    'remaining_quantity' => $item->quantity,
                    'expiry_date' => $item->expiry_date?->format('Y-m-d'),
                    'received_date' => $item->created_at?->format('Y-m-d'),
                    'size' => $item->size->size_name ?? 'N/A',
                    'color' => $item->color->color_name ?? 'N/A',
                    'is_expiring_soon' => $item->isExpiringSoon(),
                    'is_expired' => $item->isExpired(),
                    'days_until_expiry' => $item->getDaysUntilExpiry(),
                ];
            });

        return response()->json($inventoryItems);
    }

    public function getMovements(Request $request)
    {
        $query = InventoryMovement::with('product');

        if ($request->has('product_id') && $request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('movement_type') && $request->movement_type) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->has('start_date') && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $movements = $query->latest()
            ->paginate(50)
            ->through(function ($movement) {
                return [
                    'id' => $movement->id,
                    'product_name' => $movement->product->product_name ?? 'Unknown',
                    'movement_type' => $movement->movement_type,
                    'quantity' => $movement->quantity,
                    'unit_cost' => $movement->unit_cost,
                    'total_value' => $movement->total_value,
                    'reference_type' => $movement->reference_type,
                    'reference_id' => $movement->reference_id,
                    'reason' => $movement->reason,
                    'created_at' => $movement->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json($movements);
    }

    public function getLowStock()
    {
        $lowStockProducts = ProductInventory::with('product')
            ->where('quantity', '<=', 10)
            ->where('quantity', '>', 0)
            ->get()
            ->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->product_name ?? 'Unknown',
                    'current_stock' => $item->quantity,
                    'status' => 'low_stock',
                ];
            });

        return response()->json($lowStockProducts);
    }

    public function getExpiryAlerts()
    {
        $expiringStock = ProductInventory::with('product')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('quantity', '>', 0)
            ->get();

        $expiredStock = ProductInventory::with('product')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now())
            ->where('quantity', '>', 0)
            ->get();

        $alerts = collect();

        foreach ($expiringStock as $stock) {
            $alerts->push([
                'type' => 'expiring_soon',
                'product_name' => $stock->product->product_name ?? 'Unknown',
                'batch_number' => $stock->batch_number,
                'quantity' => $stock->quantity,
                'expiry_date' => $stock->expiry_date->format('Y-m-d'),
                'days_until_expiry' => $stock->getDaysUntilExpiry(),
            ]);
        }

        foreach ($expiredStock as $stock) {
            $alerts->push([
                'type' => 'expired',
                'product_name' => $stock->product->product_name ?? 'Unknown',
                'batch_number' => $stock->batch_number,
                'quantity' => $stock->quantity,
                'expiry_date' => $stock->expiry_date->format('Y-m-d'),
                'days_expired' => abs($stock->getDaysUntilExpiry()),
            ]);
        }

        return response()->json($alerts);
    }








}
