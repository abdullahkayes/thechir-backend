<?php

namespace App\Http\Controllers;

use App\Mail\OrderMail;
use App\Models\Billing;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductInventory;
use App\Models\StripeOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use App\Services\InventoryService;

class StripePaymentController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe(Request $request): View
    {
        $order_id = $request->query('order_id');
        \Log::info('Stripe payment page requested', ['order_id' => $order_id]);
        
        $order = StripeOrder::where('order_id', $order_id)->first();

        if (!$order) {
            \Log::error('Order not found for Stripe payment', ['order_id' => $order_id]);
            abort(404, 'Order not found');
        }

        \Log::info('Found order for payment', [
            'order_id' => $order_id,
            'total' => $order->total,
            'email' => $order->email
        ]);

        // Ensure minimum amount of $0.50 for Stripe
        $amountInCents = $order->total * 100;
        if ($amountInCents < 50) {
            \Log::error('Order total too low for Stripe payment', [
                'order_id' => $order_id,
                'total' => $order->total,
                'amount_cents' => $amountInCents
            ]);
            return redirect('http://localhost:5173/#')->with('error', 'Order total must be at least $0.50');
        }

        try {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $paymentIntent = Stripe\PaymentIntent::create([
                'amount' => $order->total * 100, // Stripe expects amount in cents
                'currency' => 'usd',
                'description' => 'Payment for order ' . $order_id,
                'metadata' => [
                    'order_id' => $order_id,
                    'email' => $order->email,
                ],
            ]);

            \Log::info('PaymentIntent created successfully', [
                'order_id' => $order_id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'status' => $paymentIntent->status
            ]);

            return view('stripe', compact('order_id', 'paymentIntent', 'order'));
        } catch (\Exception $e) {
            \Log::error('Stripe Payment Intent creation failed', [
                'error' => $e->getMessage(),
                'order_id' => $order_id,
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            abort(500, 'Payment system error: ' . $e->getMessage());
        }
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request, $order_id): RedirectResponse
    {
        try {
            \Log::info('=== STRIPE PAYMENT CONFIRMATION START ===', ['order_id' => $order_id]);
            
            $data = StripeOrder::where('order_id', $order_id)->first();

            if (!$data) {
                \Log::error('Stripe payment failed: Order not found', ['order_id' => $order_id]);
                return redirect()->back()->with('error', 'Order not found');
            }

            \Log::info('Found StripeOrder record', [
                'order_id' => $order_id,
                'amount' => $data->total,
                'email' => $data->email,
                'payment_status' => $request->payment_status
            ]);

            // Check payment status from frontend
            $paymentStatus = $request->payment_status;
            if ($paymentStatus === 'error') {
                \Log::error('Payment failed on frontend', ['order_id' => $order_id]);
                return redirect('http://localhost:5173/#')->with('error', 'Payment was declined. Please try again.');
            } elseif ($paymentStatus === 'requires_action') {
                \Log::info('Payment requires additional action', ['order_id' => $order_id]);
                return redirect('http://localhost:5173/#')->with('error', 'Payment requires additional verification. Please try again.');
            } elseif ($paymentStatus === 'processing') {
                \Log::info('Payment is processing', ['order_id' => $order_id]);
                // For processing, we can still process the order but maybe mark as pending
            } elseif ($paymentStatus !== 'succeeded') {
                \Log::error('Unknown payment status', ['order_id' => $order_id, 'status' => $paymentStatus]);
                return redirect('http://localhost:5173/#')->with('error', 'Payment status unknown. Please contact support.');
            }

            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            // Verify that the API key is properly set
            if (empty(env('STRIPE_SECRET'))) {
                \Log::error('Stripe Secret Key not configured', ['order_id' => $order_id]);
                return redirect()->back()->with('error', 'Payment system not properly configured');
            }

            \Log::info('Stripe API key configured successfully');
            \Log::info('Processing payment for order', [
                'order_id' => $order_id,
                'total' => $data->total,
                'currency' => 'usd'
            ]);

        // Determine user type and ID
        $userData = [];
        if ($data['coustomer_id']) {
            $userData['coustomer_id'] = $data['coustomer_id'];
            $cartQuery = Cart::with('rel_to_product')->where('coustomer_id', $data['coustomer_id']);
        } elseif ($data['reseller_id']) {
            $userData['reseller_id'] = $data['reseller_id'];
            $cartQuery = Cart::with('rel_to_product')->where('reseller_id', $data['reseller_id']);
        } elseif ($data['b2b_id']) {
            $userData['b2b_id'] = $data['b2b_id'];
            $cartQuery = Cart::with('rel_to_product')->where('b2b_id', $data['b2b_id']);
        } elseif ($data['distributer_id']) {
            $userData['distributer_id'] = $data['distributer_id'];
            $cartQuery = Cart::with('rel_to_product')->where('distributer_id', $data['distributer_id']);
        } elseif ($data['amazon_id']) {
            $userData['amazon_id'] = $data['amazon_id'];
            $cartQuery = Cart::with('rel_to_product')->where('amazon_id', $data['amazon_id']);
        } else {
            return redirect()->back()->with('error', 'Invalid user data');
        }

        $carts = $cartQuery->get();
        $carts->each(function($cart) {
            $cart->inventory = $cart->inventory;
        });

        \Log::info('Retrieved cart items for order', [
            'order_id' => $order_id,
            'cart_count' => $carts->count(),
            'user_data' => $userData
        ]);

        // Insert into Order table (skip if already exists for Stripe orders)
        if (!Order::where('order_id', $order_id)->exists()) {
            $orderData = array_merge([
                'order_id' => $order_id,
                'sub_total' => $data['sub_total'],
                'total' => $data['total'],
                'discount' => $data['discount'],
                'payment_method' => $data['payment_method'],
                'coupon' => $data['coupon'],
                'created_at' => Carbon::now(),
            ], $userData);

            try {
                Order::insert($orderData);
                \Log::info('Order inserted successfully', ['order_id' => $order_id]);
            } catch (\Exception $e) {
                \Log::error('Failed to insert order', [
                    'order_id' => $order_id,
                    'error' => $e->getMessage(),
                    'order_data' => $orderData
                ]);
                throw $e;
            }
        } else {
            \Log::info('Order already exists, skipping insert', ['order_id' => $order_id]);
        }

        // Insert into Billing table (skip if already exists)
        if (!Billing::where('order_id', $order_id)->exists()) {
            $billingData = array_merge([
                'order_id' => $order_id,
                'name' => $data['name'],
                'company' => $data['company'],
                'street' => $data['street'],
                'apartment' => $data['apartment'],
                'city' => $data['city'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'created_at' => Carbon::now(),
            ], $userData);

            try {
                Billing::insert($billingData);
                \Log::info('Billing information inserted', ['order_id' => $order_id]);
            } catch (\Exception $e) {
                \Log::error('Failed to insert billing', [
                    'order_id' => $order_id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        } else {
            \Log::info('Billing already exists, skipping insert', ['order_id' => $order_id]);
        }

        // Insert Order Products (skip if already exist)
        $existingProductCount = OrderProduct::where('order_id', $order_id)->count();
        if ($existingProductCount == 0) {
            try {
                foreach($carts as $cart) {
                    OrderProduct::insert([
                        'order_id' => $order_id,
                        'product_id' => $cart->product_id,
                        'color_id' => $cart->color_id,
                        'size_id' => $cart->size_id,
                        'quantity' => $cart->quantity,
                        'price' => $cart->inventory ? $cart->inventory->discount_price : $cart->price,
                        'created_at' => Carbon::now(),
                    ]);
                }
                \Log::info('Order products inserted', [
                    'order_id' => $order_id,
                    'product_count' => $carts->count()
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to insert order products', [
                    'order_id' => $order_id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        } else {
            \Log::info('Order products already exist, skipping insert', ['order_id' => $order_id, 'existing_count' => $existingProductCount]);
        }

        // Clear cart after successful order
        $cartDeleteQuery = Cart::query();
        if (isset($userData['coustomer_id'])) {
            $cartDeleteQuery->where('coustomer_id', $userData['coustomer_id']);
        } elseif (isset($userData['reseller_id'])) {
            $cartDeleteQuery->where('reseller_id', $userData['reseller_id']);
        } elseif (isset($userData['b2b_id'])) {
            $cartDeleteQuery->where('b2b_id', $userData['b2b_id']);
        } elseif (isset($userData['distributer_id'])) {
            $cartDeleteQuery->where('distributer_id', $userData['distributer_id']);
        } elseif (isset($userData['amazon_id'])) {
            $cartDeleteQuery->where('amazon_id', $userData['amazon_id']);
        }
        
        try {
            $cartDeleteQuery->delete();
            \Log::info('Cart cleared after order', ['order_id' => $order_id]);
        } catch (\Exception $e) {
            \Log::error('Failed to clear cart', [
                'order_id' => $order_id,
                'error' => $e->getMessage()
            ]);
        }

        // Send confirmation email
        try {
            Mail::to($data['email'])->send(new OrderMail($order_id));
            \Log::info('Order confirmation email sent', [
                'order_id' => $order_id,
                'email' => $data['email']
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send order email', [
                'order_id' => $order_id,
                'error' => $e->getMessage()
            ]);
            // Don't fail the order if email fails
        }

        // Deduct inventory after order confirmation
        foreach($carts as $cart){
            try {
                $this->inventoryService->deductStock(
                    $cart->product_id,
                    $cart->quantity,
                    'App\Models\Order',
                    $order_id
                );

                // Also reduce ProductInventory quantity
        $query = ProductInventory::where('product_id', $cart->product_id);

        if ($cart->size_id) {
            $query->where('size_id', $cart->size_id);
        } else {
            $query->whereNull('size_id');
        }

        if ($cart->color_id) {
            $query->where('color_id', $cart->color_id);
        } else {
            $query->whereNull('color_id');
        }

        $productInventory = $query->first();

        if ($productInventory) {
            $productInventory->quantity = max(0, $productInventory->quantity - $cart->quantity);
            $productInventory->save();
        }
    } catch (\Exception $e) {
        // Log error but don't fail the order
        \Log::error('Inventory deduction failed for order ' . $order_id . ': ' . $e->getMessage());
    }
  }

            \Log::info('=== STRIPE PAYMENT CONFIRMATION SUCCESS ===', [
                'order_id' => $order_id,
                'status' => 'completed',
                'timestamp' => now()
            ]);

            // Redirect to frontend order success page
            // Using the configured FRONTEND_URL or default to localhost:5173
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            
            // Ensure the hash is appended correctly
            if (strpos($frontendUrl, '#') === false) {
                $redirectUrl = rtrim($frontendUrl, '/') . '/#/order/success/' . $order_id;
            } else {
                $redirectUrl = rtrim($frontendUrl, '/') . '/order/success/' . $order_id;
            }
            
            \Log::info('Payment processing completed - redirecting to order success page', [
                'order_id' => $order_id,
                'redirect_url' => $redirectUrl
            ]);
            
            return redirect($redirectUrl);
        } catch (\Exception $e) {
            \Log::error('=== STRIPE PAYMENT PROCESSING FAILED ===', [
                'order_id' => $order_id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Payment processing failed. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Handle Stripe webhooks
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        // If webhook secret is not configured, log warning but don't fail
        if (empty($endpoint_secret)) {
            \Log::warning('Stripe webhook secret not configured');
            // For development, you can bypass signature verification
            // In production, this should always be configured
        }

        try {
            if (!empty($endpoint_secret)) {
                $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
            } else {
                // Development mode - parse JSON directly without signature verification
                $event = json_decode($payload);
            }
        } catch (\UnexpectedValueException $e) {
            \Log::error('Invalid payload in webhook: ' . $e->getMessage());
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            \Log::error('Invalid signature in webhook: ' . $e->getMessage());
            return response('Invalid signature', 400);
        }

        if (isset($event->type) && $event->type == 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $order_id = isset($paymentIntent->metadata->order_id) ? $paymentIntent->metadata->order_id : null;

            if (!$order_id) {
                \Log::error('Payment intent succeeded but no order_id in metadata');
                return response('Order ID not found', 200);
            }

            \Log::info('Payment intent succeeded via webhook', ['order_id' => $order_id, 'payment_intent_id' => $paymentIntent->id]);

            // Update the payment status in your database if needed
            // Mark order as paid
        }

        return response('Webhook handled', 200);
    }

    /**
     * Order success page
     */
    public function orderSuccess($order_id)
    {
        return view('order_success', compact('order_id'));
    }
}

