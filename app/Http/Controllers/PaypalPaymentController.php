<?php

namespace App\Http\Controllers;

use App\Mail\OrderMail;
use App\Models\Billing;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductInventory;
use App\Models\PaypalOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use App\Services\InventoryService;
use Srmklive\PayPal\Services\PayPal;

class PaypalPaymentController extends Controller
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
    public function paypal(Request $request): View
    {
        $order_id = $request->query('order_id');
        return view('paypal', compact('order_id'));
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function paypalPost(Request $request, $order_id): RedirectResponse
    {
        try {
            $data = PaypalOrder::where('order_id', $order_id)->first();

            if (!$data) {
                return redirect()->back()->with('error', 'Order not found');
            }

            // Verify PayPal payment
            if ($request->has('paypal_order_id')) {
                $paypal = new PayPal;
                $paypalOrder = $paypal->showOrderDetails($request->paypal_order_id);

                if (!isset($paypalOrder['status']) || $paypalOrder['status'] !== 'COMPLETED') {
                    return redirect()->back()->with('error', 'Payment verification failed');
                }

                // Check amount (total already includes shipping and is in USD)
                $paidAmount = $paypalOrder['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? 0;
                $expectedAmount = number_format($data->total, 2, '.', '');
                if (abs($paidAmount - $expectedAmount) > 0.01) {
                    return redirect()->back()->with('error', 'Payment amount mismatch');
                }
            } else {
                // Fallback if no verification data (should not happen in production)
                return redirect()->back()->with('error', 'Payment verification data missing');
            }

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

        if ($carts->isEmpty()) {
            return redirect()->back()->with('error', 'No items found in cart');
        }

        $carts->each(function($cart) {
            $cart->inventory = $cart->inventory;
        });

        // Insert into Order table
        $orderData = array_merge([
            'order_id' => $order_id,
            'sub_total' => $data['sub_total'],
            'total' => $data['total'],
            'discount' => $data['discount'],
            'delivery_charge' => $data['delivery_charge'] ?? 0,
            'payment_method' => $data['payment_method'],
            'coupon' => $data['coupon'],
            'created_at' => Carbon::now(),
        ], $userData);

        Order::insert($orderData);

        // Insert into Billing table
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

        Billing::insert($billingData);

        // Insert Order Products
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
        $cartDeleteQuery->delete();

        Mail::to($data['email'])->send(new OrderMail($order_id));

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

        // Redirect to frontend order success page
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        
        if (strpos($frontendUrl, '#') === false) {
            $redirectUrl = rtrim($frontendUrl, '/') . '/#/order/success/' . $order_id;
        } else {
            $redirectUrl = rtrim($frontendUrl, '/') . '/order/success/' . $order_id;
        }
        
        return redirect($redirectUrl);
        } catch (\Exception $e) {
            \Log::error('PayPal payment processing failed for order ' . $order_id . ': ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'An error occurred while processing your order. Please try again.');
        }
    }

    /**
     * Order success page
     */
    public function orderSuccess($order_id)
    {
        return view('order_success', compact('order_id'));
    }
}