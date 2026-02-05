<?php

namespace App\Http\Controllers;

use App\Models\QRPayment;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PaymentApprovedMail;
use App\Services\InventoryService;

class QRPaymentController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }
    /**
     * Handle OPTIONS preflight requests for CORS
     */
    public function handleOptions()
    {
        return response('', 204)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, X-CSRF-TOKEN')
            ->header('Access-Control-Allow-Credentials', 'true');
    }

    /**
     * Display pending QR payments for admin
     */
    public function index()
    {
        $pendingPayments = QRPayment::with(['order', 'approvedBy'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedPayments = QRPayment::with(['order'])
            ->approved()
            ->orderBy('approved_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.qr-payments', compact('pendingPayments', 'approvedPayments'));
    }

    /**
     * Submit QR payment (called from frontend via API)
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'payment_type' => 'required|in:venmo,cashapp',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'amount' => 'required|numeric|min:0.01',
            'transaction_id' => 'required|string|max:255',
            'screenshot_base64' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        // Handle base64 screenshot
        $screenshotPath = null;
        if ($request->screenshot_base64) {
            // Extract base64 data (remove data:image/png;base64, prefix)
            $base64Data = $request->screenshot_base64;
            if (strpos($base64Data, ',') !== false) {
                $base64Data = explode(',', $base64Data)[1];
            }
            
            // Decode and save
            $imageData = base64_decode($base64Data);
            $filename = 'qr-payment-' . uniqid() . '.png';
            $screenshotPath = 'qr-payment-screenshots/' . $filename;
            
            Storage::disk('public')->put($screenshotPath, $imageData);
        }

        $qrPayment = QRPayment::create([
            'order_id' => $request->order_id,
            'payment_type' => $request->payment_type,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'amount' => $request->amount,
            'transaction_id' => $request->transaction_id,
            'screenshot_path' => $screenshotPath,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment submitted successfully! Please wait for admin approval.',
            'payment_id' => $qrPayment->id,
        ]);
    }

    /**
     * Approve a QR payment
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $qrPayment = QRPayment::findOrFail($id);

        if ($qrPayment->status !== 'pending') {
            return redirect()->back()->with('error', 'Payment has already been processed.');
        }

        // Update QR payment status
        $qrPayment->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Load the associated order with its order products
        $order = Order::where('order_id', $qrPayment->order_id)->with('orderProducts.product')->first();
        
        if ($order) {
            // Update order status to processing (confirmed payment)
            $order->update([
                'status' => 'processing',
                'payment_status' => 'paid',
            ]);
            
            Log::info('QR Payment approved - Order status updated', [
                'order_id' => $order->order_id,
                'qr_payment_id' => $qrPayment->id,
                'status' => 'processing',
                'payment_status' => 'paid'
            ]);

            // Deduct inventory for each order product
            foreach ($order->orderProducts as $orderProduct) {
                try {
                    // First try to deduct using InventoryService
                    $this->inventoryService->deductStock(
                        $orderProduct->product_id,
                        $orderProduct->quantity,
                        'App\\Models\\Order',
                        $order->id
                    );
                    
                    Log::info('Inventory deducted via InventoryService', [
                        'product_id' => $orderProduct->product_id,
                        'quantity' => $orderProduct->quantity,
                        'order_id' => $order->order_id
                    ]);
                } catch (\Exception $e) {
                    Log::warning('InventoryService deduction failed, falling back to direct update', [
                        'product_id' => $orderProduct->product_id,
                        'error' => $e->getMessage()
                    ]);
                    
                    // Fallback: Direct inventory update if InventoryService fails
                    try {
                        $query = ProductInventory::where('product_id', $orderProduct->product_id);
                        
                        if ($orderProduct->size_id) {
                            $query->where('size_id', $orderProduct->size_id);
                        } else {
                            $query->whereNull('size_id');
                        }
                        
                        if ($orderProduct->color_id) {
                            $query->where('color_id', $orderProduct->color_id);
                        } else {
                            $query->whereNull('color_id');
                        }
                        
                        $productInventory = $query->first();
                        
                        if ($productInventory) {
                            $productInventory->quantity = max(0, $productInventory->quantity - $orderProduct->quantity);
                            $productInventory->save();
                            
                            Log::info('Inventory deducted directly', [
                                'product_id' => $orderProduct->product_id,
                                'quantity_deducted' => $orderProduct->quantity,
                                'new_quantity' => $productInventory->quantity
                            ]);
                        }
                    } catch (\Exception $directError) {
                        Log::error('Direct inventory deduction failed', [
                            'product_id' => $orderProduct->product_id,
                            'error' => $directError->getMessage()
                        ]);
                    }
                }
            }
        } else {
            Log::warning('Order not found for QR payment approval', [
                'order_id' => $qrPayment->order_id,
                'qr_payment_id' => $qrPayment->id
            ]);
        }

        // Send approval email
        Mail::to($qrPayment->customer_email)->send(new PaymentApprovedMail($qrPayment));

        return redirect()->back()->with('success', 'Payment approved, order status updated, inventory deducted, and email sent to customer.');
    }

    /**
     * Reject a QR payment
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $qrPayment = QRPayment::findOrFail($id);

        if ($qrPayment->status !== 'pending') {
            return redirect()->back()->with('error', 'Payment has already been processed.');
        }

        // Update QR payment status
        $qrPayment->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Update associated order status to cancelled/payment failed
        $order = Order::where('order_id', $qrPayment->order_id)->first();
        
        if ($order) {
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
            ]);
            
            Log::info('QR Payment rejected - Order cancelled', [
                'order_id' => $order->order_id,
                'qr_payment_id' => $qrPayment->id,
                'reason' => $request->admin_notes
            ]);
        }

        return redirect()->back()->with('success', 'Payment rejected and order cancelled.');
    }

    /**
     * Delete a QR payment
     */
    public function destroy($id)
    {
        $qrPayment = QRPayment::findOrFail($id);

        // Delete screenshot if exists
        if ($qrPayment->screenshot_path && Storage::disk('public')->exists($qrPayment->screenshot_path)) {
            Storage::disk('public')->delete($qrPayment->screenshot_path);
        }

        $qrPayment->delete();

        return redirect()->back()->with('success', 'Payment record deleted.');
    }

    /**
     * Get QR payment details (for admin modal)
     */
    public function show($id)
    {
        $qrPayment = QRPayment::with(['order', 'approvedBy'])->findOrFail($id);

        return response()->json([
            'payment' => $qrPayment,
            'screenshot_url' => $qrPayment->screenshot_path ? Storage::url($qrPayment->screenshot_path) : null,
        ]);
    }

    /**
     * Get payment statistics
     */
    public function statistics()
    {
        $pending = QRPayment::pending()->count();
        $approved = QRPayment::approved()->count();
        $rejected = QRPayment::rejected()->count();
        $totalAmount = QRPayment::approved()->sum('amount');

        return response()->json([
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'total_amount' => $totalAmount,
        ]);
    }
}
