<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    
    function order(Request $request){
        $query = Order::query();

        if($request->startDate && $request->endDate){
            $start = Carbon::parse($request->startDate)->startOfDay();
            $end = Carbon::parse($request->endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }
        elseif($request->startDate){
            $start = Carbon::parse($request->startDate)->startOfDay();
            $query->whereBetween('created_at', '>=', $start );
        }
        elseif($request->endDate){
            $end = Carbon::parse($request->endDate)->startOfDay();
            $query->whereBetween('created_at', '<=', $end );
        }

        $orders = $query->latest()->get();

        // Order by month
        $MonthlyOrders = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->when($request->startDate, function($q) use ($request){
                $q->whereDate('created_at', '>=', $request->startDate);
            })->when($request->endDate, function($q) use ($request){
                $q->whereDate('created_at', '<=', $request->endDate);
            })->groupBy('month')->orderBy('month')->pluck('total','month');

        $orderData = [];
        for($i=1; $i<=12; $i++){
            $orderData[] = $MonthlyOrders[$i] ?? 0;
        }

        // Order by day
        $dailyOrders = Order::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->when($request->startDate, function($q) use ($request){
                $q->whereDate('created_at', '>=', $request->startDate);
            })->when($request->endDate, function($q) use ($request){
                $q->whereDate('created_at', '<=', $request->endDate);
            })->groupBy('day')->orderBy('day')->pluck('total','day');

        $days = $dailyOrders->keys()->toArray();
        $dayWiseData = $dailyOrders->values()->toArray();

        // Sales by month
        $salesMonth = Order::selectRaw('MONTH(created_at) as month, SUM(total) as total_amount')
            ->when($request->startDate, function($q) use ($request){
                $q->whereDate('created_at', '>=', $request->startDate);
            })->when($request->endDate, function($q) use ($request){
                $q->whereDate('created_at', '<=', $request->endDate);
            })->groupBy('month')->orderBy('month')->pluck('total_amount','month');

        $salesData = [];
        for($i=1; $i<=12; $i++){
            $salesData[] = $salesMonth[$i] ?? 0;
        }

        return view('Backend.order.order', compact('orders', 'orderData', 'days', 'dayWiseData', 'salesData'));
    }
    
    function status_change(Request $request, $id){
        // Validate status value
        $request->validate([
            'status' => 'required|integer|in:0,1,2,3,4'
        ]);

        $order = Order::findOrFail($id);

        // If status is being changed to delivered (3) and wasn't already delivered, fulfill the order
        if ($request->status == 3 && $order->status != 3) {
            try {
                $this->orderService->fulfillOrder($order);
            } catch (\Exception $e) {
                \Log::error('Order fulfillment failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
                return back()->with('error', 'Failed to fulfill order: ' . $e->getMessage());
            }
        }

        \Log::info('Updating order status', [
            'order_id' => $order->id,
            'old_status' => $order->status,
            'new_status' => $request->status
        ]);

        // Update order status
        $updated = $order->update([
            'status' => $request->status
        ]);

        \Log::info('Order status update result', [
            'order_id' => $order->id,
            'updated' => $updated,
            'current_status' => $order->fresh()->status
        ]);

        // Update order tracking status if exists
        $orderTracking = OrderTracking::where('order_id', $order->order_id)->first();
        if ($orderTracking) {
            $orderTracking->update(['status' => $request->status]);
        }

        if ($updated) {
            return back()->with('success', 'Order status updated successfully!');
        } else {
            return back()->with('error', 'Failed to update order status');
        }
    }
    
    function invoice($order_id){
        $data = Order::where('order_id', $order_id)->firstOrFail();
        $pdf = Pdf::loadView('pdf.invoice', compact('data'));
        return $pdf->download('invoice_'.$order_id.'.pdf')->withHeaders([
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice_'.$order_id.'.pdf"',
        ]);
    }
     
    function invoice_print($order_id){
        $data = Order::where('order_id', $order_id)->firstOrFail();
        $pdf = Pdf::loadView('pdf.invoice', compact('data'));
        return $pdf->stream();
    }
}
