<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderTracking;
use App\Services\OrderService;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display returns index
     */
    public function index()
    {
        // Get orders that have been returned (status 4) OR orders that have been processed as returns
        $returns = Order::where(function($query) {
                $query->whereHas('orderTracking', function ($q) {
                    $q->where('status', 4); // Orders with tracking status 4 (returned)
                });
            })
            ->orWhere(function($query) {
                // Also include orders that might have been returned but don't have tracking
                // This is a fallback for orders processed through the return system
                $query->where('status', 5); // Assuming 5 could be a returned status
            })
            ->with(['customer', 'orderProducts.product', 'orderTracking'])
            ->latest()
            ->paginate(15);

        return view('returns.index', compact('returns'));
    }

    /**
     * Process a return request
     */
    public function processReturn(Request $request, Order $order)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'return_items' => 'required|array',
                'return_items.*.order_product_id' => 'required|exists:order_products,id',
                'return_items.*.quantity' => 'required|integer|min:1',
                'return_items.*.type' => 'required|in:resellable,damaged',
                'return_items.*.reason' => 'required|string',
            ]);

            $result = $this->orderService->processReturn($order, $validatedData['return_items']);

            // Update order tracking status to returned
            $orderTracking = $order->orderTracking;
            if ($orderTracking) {
                $orderTracking->update(['status' => 4]); // Assuming 4 is returned
            } else {
                // Create order tracking if it doesn't exist
                // Use $order->id (integer) instead of $order->order_id (string)
                OrderTracking::create([
                    'order_id' => $order->id,
                    'status' => 4, // Returned status
                    'description' => 'Order has been returned and processed'
                ]);
            }

            // Also update the order status to mark it as returned
            $order->update(['status' => 5]); // Assuming 5 is returned status

            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Return processed successfully.',
                    'refund' => $result['refund'] ?? 0
                ]);
            }

            return redirect()->back()->with('success', 'Return processed successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            // For non-AJAX requests, let Laravel handle the validation exception normally
            throw $e;
        } catch (\Exception $e) {
            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process return: ' . $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', 'Failed to process return: ' . $e->getMessage());
        }
    }

    /**
     * Show return form for an order
     */
    public function create(Order $order)
    {
        $order->load(['orderProducts.product', 'customer']);
        return view('returns.create', compact('order'));
    }

    /**
     * API endpoint to get returnable orders
     */
    public function getReturnableOrders()
    {
        $orders = Order::with(['customer', 'orderProducts.product', 'orderTracking'])
            ->latest()
            ->get()
            ->map(function ($order) {
                // Get tracking status, default to 1 if no tracking exists
                $trackingStatus = $order->orderTracking ? $order->orderTracking->status : 1;
                
                return [
                    'id' => $order->id,
                    'order_id' => $order->order_id,
                    'customer_name' => $order->customer->name ?? 'N/A',
                    'total' => $order->total,
                    'status' => $order->status,
                    'tracking_status' => $trackingStatus,
                    'created_at' => $order->created_at->format('Y-m-d'),
                    'products' => $order->orderProducts->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_name' => $item->product->product_name ?? 'Unknown',
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                        ];
                    }),
                ];
            });

        return response()->json($orders);
    }

    /**
     * Show return details
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'orderProducts.product', 'orderTracking']);
        
        // Check if this order has return tracking
        $isReturned = $order->orderTracking && $order->orderTracking->status == 4;
        
        return view('returns.show', compact('order', 'isReturned'));
    }
}