<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductInventory;
use App\Models\Commission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Mail\OrderMail;
use App\Models\StripeOrder;
use Illuminate\Support\Facades\Mail;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;


class CheckoutController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Mark commissions as used for balance deduction
     */
    private function markCommissionsAsUsed($resellerId, $amountToUse, $orderId)
    {
        $commissions = Commission::where('reseller_id', $resellerId)
            ->where('status', 'available')
            ->orderBy('created_at', 'asc')
            ->get();

        $remainingAmount = $amountToUse;

        foreach ($commissions as $commission) {
            if ($remainingAmount <= 0) break;

            if ($commission->amount <= $remainingAmount) {
                // Use entire commission
                $commission->update([
                    'status' => 'used',
                    'used_in_order_id' => $orderId
                ]);
                $remainingAmount -= $commission->amount;
            } else {
                // Partial use - this shouldn't happen with current logic, but handle it
                // For now, we'll use entire commissions only
                // Could be enhanced to split commissions if needed
                break;
            }
        }

        Log::info('Marked commissions as used', [
            'reseller_id' => $resellerId,
            'amount_used' => $amountToUse,
            'remaining' => $remainingAmount
        ]);
    }

    /**
     * Safely get authenticated user from any guard
     */
    private function getAuthenticatedUser()
    {
        // First try to get user from request (set by middleware)
        if (request()->user()) {
            return request()->user();
        }

        // Fallback: manually check token for all user types
        $token = request()->bearerToken();
        if ($token) {
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
                if ($user instanceof \App\Models\Coustomer || $user instanceof \App\Models\Reseller ||
                    $user instanceof \App\Models\B2b || $user instanceof \App\Models\Distributer ||
                    $user instanceof \App\Models\Amazon) {
                    return $user;
                }
            }
        }

        return null;
    }

    function apply_coupon(Request $request){
        $coupon = $request->coupon;
     if(Coupon::where('coupon',$coupon)->exists()){
   if(Carbon::now()->format('Y-m-d')<=Coupon::where('coupon',$coupon)->first()->validity){
    return response()->json([
        'amount'=>Coupon::where('coupon',$coupon)->first()->amount
    ]);
   }else{
    return response()->json([
        'notexists'=>'Coupon Expired'
    ]);
   }
}else{
    return response()->json([
        'notexists'=>'Coupon Dose Not Exists'
    ]);
}
    }

function checkout(Request $request){
    $total_quantity = 0;
    $total_sell_price = 0;
    $total_cost_price = 0;

    try {
        Log::info('Checkout request received', $request->all());

        // Detect user type and get user data
        $user = $this->getAuthenticatedUser();
        if (!$user) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $userData = [];
        $balanceUsed = 0;
        $payableAmount = $request->total;

        if ($user instanceof \App\Models\Coustomer || $user instanceof \App\Models\User) {
            $userData['coustomer_id'] = $user->id;
        } elseif ($user instanceof \App\Models\Reseller) {
            $userData['reseller_id'] = $user->id;

            // For resellers, calculate balance used based on the difference between sub_total and total
            // The frontend deducts balance, so if total < sub_total, balance was used
            $availableBalance = $user->commissions()->where('status', 'available')->sum('amount');
            $originalTotal = $request->sub_total;
            
            Log::info('Reseller checkout - Available balance: ' . $availableBalance . ', Subtotal: ' . $originalTotal . ', Payable amount: ' . $request->total);
            
            // Calculate how much balance was used (difference between subtotal and total)
            $calculatedBalanceUsed = max(0, $originalTotal - $request->total);
            
            // Use the calculated amount or the frontend-provided balance_used if available
            if ($request->has('balance_used') && $request->balance_used > 0) {
                $balanceUsed = $request->balance_used;
            } else {
                $balanceUsed = $calculatedBalanceUsed;
            }
            
            Log::info('Balance used in this order: ' . $balanceUsed);
        } elseif ($user instanceof \App\Models\B2b) {
            $userData['b2b_id'] = $user->id;
        } elseif ($user instanceof \App\Models\Distributer) {
            $userData['distributer_id'] = $user->id;
        } elseif ($user instanceof \App\Models\Amazon) {
            $userData['amazon_id'] = $user->id;
        } else {
            return response()->json(['error' => 'Invalid user type'], 403);
        }

        $order_id = uniqid();
        Log::info('Generated order_id: ' . $order_id);

        $refId = null;
        $resellerId = null;
        $reseller = null;

        Log::info('Checking for reseller referral', [
            'user_type' => get_class($user),
            'user_id' => $user->id,
            'user_ref_id' => $user->ref_id ?? null,
            'cookie_ref_id' => Cookie::get('ref_id') ?? null,
        ]);

        // For B2B users, get reseller from their ref_id field
        if ($user instanceof \App\Models\B2b && $user->ref_id) {
            $refId = $user->ref_id;
            $reseller = \App\Models\Reseller::where('unique_ref_id', $refId)->first();
            if ($reseller) {
                $resellerId = $reseller->id;
                Log::info('Found reseller from B2B ref_id', ['reseller_id' => $resellerId, 'ref_id' => $refId]);
            } else {
                Log::warning('Reseller not found for ref_id', ['ref_id' => $refId]);
            }
        }
        // For other user types, check cookie as fallback (though primarily for B2B now)
        elseif (Cookie::get('ref_id')) {
            $refId = Cookie::get('ref_id');
            $reseller = \App\Models\Reseller::where('unique_ref_id', $refId)->first();
            if ($reseller) {
                $resellerId = $reseller->id;
                Log::info('Found reseller from cookie', ['reseller_id' => $resellerId, 'ref_id' => $refId]);
            } else {
                Log::warning('Reseller not found in cookie', ['ref_id' => $refId]);
            }
        }

        Log::info('Final reseller status', [
            'has_reseller_id' => !is_null($resellerId),
            'reseller_id' => $resellerId,
        ]);

        if($request->payment_method == '1'){
            Log::info('Processing payment method 1 (Cash on Delivery) - using orders table');
            
            // Validate required fields for Cash on Delivery
            $requiredFields = ['sub_total', 'total', 'payment_method'];
            foreach ($requiredFields as $field) {
                if ($request->$field === null || $request->$field === '') {
                    Log::error("Missing required field: $field", ['request' => $request->all()]);
                    return response()->json([
                        'error' => "Missing required field: $field"
                    ], 400);
                }
            }
            
            // Ensure discount has a value (default to 0 if not provided)
            $discount = $request->discount ?? 0;
            $coupon = $request->coupon ?? '';

            Log::info('All required fields present, creating order in orders table');

            // Get cart items for the authenticated user
            $cartQuery = Cart::with('rel_to_product');
            if (isset($userData['coustomer_id'])) {
                $cartQuery->where('coustomer_id', $userData['coustomer_id']);
            } elseif (isset($userData['reseller_id'])) {
                $cartQuery->where('reseller_id', $userData['reseller_id']);
            } elseif (isset($userData['b2b_id'])) {
                $cartQuery->where('b2b_id', $userData['b2b_id']);
            } elseif (isset($userData['distributer_id'])) {
                $cartQuery->where('distributer_id', $userData['distributer_id']);
            } elseif (isset($userData['amazon_id'])) {
                $cartQuery->where('amazon_id', $userData['amazon_id']);
            }
            $carts = $cartQuery->get();
            Log::info('Retrieved carts for user', ['count' => $carts->count(), 'user_type' => key($userData), 'user_id' => current($userData)]);

            // Calculate totals for orders table
            $total_quantity = $carts->sum('quantity');
            $total_sell_price = $carts->sum(function($cart) {
                return ($cart->sell_price ?? $cart->price ?? 0) * $cart->quantity;
            });
            $total_cost_price = $carts->sum(function($cart) {
                return ($cart->cost_price ?? 0) * $cart->quantity;
            });

            // Create order for Cash on Delivery - using orders table structure
            $orderData = array_merge([
                'order_id' => $order_id,
                'sub_total' => $request->sub_total,
                'total' => $request->total,
                'balance_used' => $balanceUsed,
                'discount' => $discount,
                'payment_method' => (int)$request->payment_method,
                'coupon' => $coupon ?: null, // Convert empty string to null for nullable field
                'payment_status' => isset($userData['amazon_id']) ? 'pending' : 'paid',
                'status' => isset($userData['amazon_id']) ? 1 : 3, // Amazon orders start as pending (1), others as completed (3)
                'quantity' => $total_quantity,
                'sell_price' => $total_sell_price,
                'cost_price' => $total_cost_price,
                'delivery_charge' => $request->shipping_cost ?? 0,
                // Note: orders table doesn't have billing fields - that's handled by Billing table
            ], $userData);
            
            Log::info('Cash on Delivery order data to insert:', $orderData);
            
            // Create the order using Eloquent create method
            $createdOrder = Order::create($orderData);
            Log::info('Order create result: ' . ($createdOrder ? 'SUCCESS' : 'FAILED'));

            if (!$createdOrder) {
                Log::error('Failed to create order', ['data' => $orderData]);
                return response()->json([
                    'error' => 'Failed to create order in database'
                ], 500);
            }
            
            Log::info('Order verified in database', ['order_id' => $order_id]);

            // Mark commissions as used if balance was applied (for resellers only)
            if ($user instanceof \App\Models\Reseller && $balanceUsed > 0) {
                $this->markCommissionsAsUsed($user->id, $balanceUsed, $createdOrder->id);
            }

            // Create commission if reseller and this is a B2B order
            if ($resellerId && $reseller && $user instanceof \App\Models\B2b) {
                // Calculate commission as 5% of the total order value
                $commissionAmount = $request->total * 0.05;

                Log::info('Creating commission for B2B order', [
                    'reseller_id' => $resellerId,
                    'order_id' => $createdOrder->id,
                    'order_total' => $request->total,
                    'commission_amount' => $commissionAmount,
                    'b2b_id' => $user->id,
                    'b2b_business_name' => $user->business_name,
                ]);

                $commission = Commission::create([
                    'reseller_id' => $resellerId,
                    'order_id' => $createdOrder->id,
                    'amount' => $commissionAmount,
                    'status' => 'pending',
                ]);

                Log::info('Commission created successfully', ['commission_id' => $commission->id]);
            } else {
                Log::warning('Commission NOT created - conditions not met', [
                    'has_reseller_id' => !is_null($resellerId),
                    'has_reseller' => !is_null($reseller),
                    'is_b2b_user' => $user instanceof \App\Models\B2b,
                ]);
            }

            // Create billing record for Cash on Delivery orders
            $billingData = array_merge([
                'order_id' => $order_id,
                'name' => $request->name,
                'company' => $request->company ?: null,
                'street' => $request->street,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'phone' => $request->phone,
                'email' => $request->email,
            ], $userData);

            Log::info('Billing data to create:', $billingData);
            $billingResult = Billing::create($billingData);
            Log::info('Billing create result: ' . ($billingResult ? 'SUCCESS' : 'FAILED'));

            // Store cart items for inventory deduction BEFORE creating order products
            $cartItemsForInventory = $carts->map(function($cart) {
                return [
                    'product_id' => $cart->product_id,
                    'color_id' => $cart->color_id,
                    'size_id' => $cart->size_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->price ?? 0,
                ];
            })->toArray();

            foreach($carts as $cart){
                OrderProduct::create([
                    'order_id' => $order_id,
                    'product_id' => $cart->product_id,
                    'color_id' => $cart->color_id,
                    'size_id' => $cart->size_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->price ?? 0,
                    'sell_price' => $cart->sell_price ?? $cart->price ?? 0,
                    'cost_price' => $cart->cost_price ?? 0,
                ]);
            }
            Log::info('Order products inserted successfully');

            // Send order confirmation email to customer and admin
            try {
                Mail::to($request->email)->send(new OrderMail($order_id));
                Log::info('Order confirmation email sent to customer');
                
                // Send copy to admin email from .env file
                $adminEmail = env('MAIL_USERNAME');
                if (!empty($adminEmail)) {
                    Mail::to($adminEmail)->send(new OrderMail($order_id));
                    Log::info('Order confirmation email sent to admin');
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send order confirmation email: ' . $e->getMessage());
            }

            // Deduct inventory using stored cart items
            foreach($cartItemsForInventory as $cartItem){
                try {
                    // Direct inventory deduction - update ProductInventory table
                    $query = ProductInventory::where('product_id', $cartItem['product_id']);

                    if ($cartItem['size_id']) {
                        $query->where('size_id', $cartItem['size_id']);
                    } else {
                        $query->whereNull('size_id');
                    }

                    if ($cartItem['color_id']) {
                        $query->where('color_id', $cartItem['color_id']);
                    } else {
                        $query->whereNull('color_id');
                    }

                    $productInventory = $query->first();

                    if ($productInventory) {
                        $newQuantity = max(0, $productInventory->quantity - $cartItem['quantity']);
                        $productInventory->update(['quantity' => $newQuantity]);
                        
                        \Log::info("Inventory updated for product {$cartItem['product_id']}: {$productInventory->quantity} -> {$newQuantity}");
                    } else {
                        \Log::warning("No inventory record found for product {$cartItem['product_id']}");
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the order
                    \Log::error('Inventory deduction failed for order ' . $order_id . ': ' . $e->getMessage(), [
                        'product_id' => $cartItem['product_id'],
                        'quantity' => $cartItem['quantity'],
                        'error' => $e->getMessage()
                    ]);
                }
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
            Log::info('Cart cleared successfully');

            Log::info('Order processing completed successfully for order_id: ' . $order_id);
            
            return response()->json([
                'order_id' => $order_id,
                'message' => 'Order created successfully'
            ]);
        }
        else if($request->payment_method == '2'){
            Log::info('Processing payment method 2 (Stripe) - using stripe_orders table');

            // Validate required fields for Stripe (includes billing info)
            $requiredFields = ['sub_total', 'total', 'payment_method', 'name', 'street', 'city', 'phone', 'email'];
            foreach ($requiredFields as $field) {
                if ($request->$field === null || $request->$field === '') {
                    Log::error("Missing required field for Stripe: $field", ['request' => $request->all()]);
                    return response()->json([
                        'error' => "Missing required field: $field"
                    ], 400);
                }
            }
            
            // Ensure discount has a value
            $discount = $request->discount ?? 0;
            $coupon = $request->coupon ?? '';

            // Get cart items for Stripe orders
            $cartQuery = Cart::with('rel_to_product');
            if (isset($userData['coustomer_id'])) {
                $cartQuery->where('coustomer_id', $userData['coustomer_id']);
            } elseif (isset($userData['reseller_id'])) {
                $cartQuery->where('reseller_id', $userData['reseller_id']);
            } elseif (isset($userData['b2b_id'])) {
                $cartQuery->where('b2b_id', $userData['b2b_id']);
            } elseif (isset($userData['distributer_id'])) {
                $cartQuery->where('distributer_id', $userData['distributer_id']);
            } elseif (isset($userData['amazon_id'])) {
                $cartQuery->where('amazon_id', $userData['amazon_id']);
            }
            $carts = $cartQuery->get();

            // Calculate totals for orders table
            $total_quantity = $carts->sum('quantity');
            $total_sell_price = $carts->sum(function($cart) {
                return ($cart->sell_price ?? $cart->price ?? 0) * $cart->quantity;
            });
            $total_cost_price = $carts->sum(function($cart) {
                return ($cart->cost_price ?? 0) * $cart->quantity;
            });

            // Create order in orders table
            $orderData = array_merge([
                'order_id' => $order_id,
                'sub_total' => $request->sub_total,
                'total' => $request->total,
                'discount' => $discount,
                'payment_method' => (int)$request->payment_method,
                'coupon' => $coupon ?: null,
                'status' => 1, // Set default status to 1 (pending)
                'quantity' => $total_quantity,
                'sell_price' => $total_sell_price,
                'cost_price' => $total_cost_price,
                'delivery_charge' => $request->shipping_cost ?? 0,
            ], $userData);

            Order::create($orderData);
            Log::info('Order inserted in orders table for Stripe');

            // Calculate final total including shipping for Stripe payment
            $final_total = $request->total + ($request->shipping_cost ?? 0);

            // Create Stripe order - using stripe_orders table structure (includes billing fields)
            $stripeOrderData = array_merge([
                'order_id' => $order_id,
                'sub_total' => $request->sub_total,
                'total' => $final_total, // Include shipping in total for payment
                'discount' => $discount,
                'payment_method' => (int)$request->payment_method,
                'coupon' => $coupon ?: null,
                'status' => 1, // Set default status to 1 (pending)
                'name' => $request->name,
                'company' => $request->company ?: null,
                'street' => $request->street,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'phone' => $request->phone,
                'email' => $request->email,
            ], $userData);

            Log::info('Stripe order data to insert:', $stripeOrderData);
            StripeOrder::insert($stripeOrderData);
            Log::info('Stripe order inserted successfully');

            // Create order products for Stripe orders
            foreach($carts as $cart){
                OrderProduct::create([
                    'order_id' => $order_id,
                    'product_id' => $cart->product_id,
                    'color_id' => $cart->color_id,
                    'size_id' => $cart->size_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->price ?? 0,
                    'sell_price' => $cart->sell_price ?? $cart->price ?? 0,
                    'cost_price' => $cart->cost_price ?? 0,
                ]);
            }
            Log::info('Order products inserted for Stripe order');

            // Get the created order for commission tracking
            $createdStripeOrder = Order::where('order_id', $order_id)->first();

            // Create commission if reseller and this is a B2B order (Stripe)
            if ($resellerId && $reseller && $user instanceof \App\Models\B2b && $createdStripeOrder) {
                $commissionAmount = $request->total * 0.05;

                Commission::create([
                    'reseller_id' => $resellerId,
                    'order_id' => $createdStripeOrder->id,
                    'amount' => $commissionAmount,
                    'status' => 'pending',
                ]);

                Log::info('Commission created for Stripe B2B order', [
                    'reseller_id' => $resellerId,
                    'order_id' => $order_id,
                    'commission_amount' => $commissionAmount
                ]);
            }

            // Send order confirmation email to customer and admin
            try {
                Mail::to($request->email)->send(new OrderMail($order_id));
                Log::info('Order confirmation email sent to customer');
                
                // Send copy to admin email from .env file
                $adminEmail = env('MAIL_USERNAME');
                if (!empty($adminEmail)) {
                    Mail::to($adminEmail)->send(new OrderMail($order_id));
                    Log::info('Order confirmation email sent to admin');
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send order confirmation email: ' . $e->getMessage());
            }

            return response()->json([
                'redirect' => url('stripe?order_id='.$order_id),
            ]);
        }
        else if($request->payment_method == '3'){
            Log::info('Processing payment method 3 (PayPal) - using paypal_orders table');

            // Validate required fields for PayPal (includes billing info)
            $requiredFields = ['sub_total', 'total', 'payment_method', 'name', 'street', 'city', 'phone', 'email'];
            foreach ($requiredFields as $field) {
                if ($request->$field === null || $request->$field === '') {
                    Log::error("Missing required field for PayPal: $field", ['request' => $request->all()]);
                    return response()->json([
                        'error' => "Missing required field: $field"
                    ], 400);
                }
            }

            // Ensure discount has a value
            $discount = $request->discount ?? 0;
            $coupon = $request->coupon ?? '';

            // Get cart items for PayPal orders
            $cartQuery = Cart::with('rel_to_product');
            if (isset($userData['coustomer_id'])) {
                $cartQuery->where('coustomer_id', $userData['coustomer_id']);
            } elseif (isset($userData['reseller_id'])) {
                $cartQuery->where('reseller_id', $userData['reseller_id']);
            } elseif (isset($userData['b2b_id'])) {
                $cartQuery->where('b2b_id', $userData['b2b_id']);
            } elseif (isset($userData['distributer_id'])) {
                $cartQuery->where('distributer_id', $userData['distributer_id']);
            } elseif (isset($userData['amazon_id'])) {
                $cartQuery->where('amazon_id', $userData['amazon_id']);
            }
            $carts = $cartQuery->get();

            // Calculate totals for orders table
            $total_quantity = $carts->sum('quantity');
            $total_sell_price = $carts->sum(function($cart) {
                return ($cart->sell_price ?? $cart->price ?? 0) * $cart->quantity;
            });
            $total_cost_price = $carts->sum(function($cart) {
                return ($cart->cost_price ?? 0) * $cart->quantity;
            });

            // Create order in orders table
            $orderData = array_merge([
                'order_id' => $order_id,
                'sub_total' => $request->sub_total,
                'total' => $request->total,
                'discount' => $discount,
                'payment_method' => (int)$request->payment_method,
                'coupon' => $coupon ?: null,
                'status' => 1, // Set default status to 1 (pending)
                'quantity' => $total_quantity,
                'sell_price' => $total_sell_price,
                'cost_price' => $total_cost_price,
                'delivery_charge' => $request->shipping_cost ?? 0,
            ], $userData);

            Order::create($orderData);
            Log::info('Order inserted in orders table for PayPal');

            // Create PayPal order - using paypal_orders table structure (includes billing fields)
            $paypalOrderData = array_merge([
                'order_id' => $order_id,
                'sub_total' => $request->sub_total,
                'total' => $request->total,
                'discount' => $discount,
                'delivery_charge' => $request->shipping_cost ?? 0,
                'payment_method' => (int)$request->payment_method,
                'coupon' => $coupon ?: null,
                'status' => 1, // Set default status to 1 (pending)
                'name' => $request->name,
                'company' => $request->company ?: null,
                'street' => $request->street,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'phone' => $request->phone,
                'email' => $request->email,
            ], $userData);

            Log::info('PayPal order data to insert:', $paypalOrderData);
            \App\Models\PaypalOrder::insert($paypalOrderData);
            Log::info('PayPal order inserted successfully');

            // Create order products for PayPal orders
            foreach($carts as $cart){
                OrderProduct::create([
                    'order_id' => $order_id,
                    'product_id' => $cart->product_id,
                    'color_id' => $cart->color_id,
                    'size_id' => $cart->size_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->price ?? 0,
                    'sell_price' => $cart->sell_price ?? $cart->price ?? 0,
                    'cost_price' => $cart->cost_price ?? 0,
                ]);
            }
            Log::info('Order products inserted for PayPal order');

            // Get the created order for commission tracking
            $createdPaypalOrder = Order::where('order_id', $order_id)->first();

            // Create commission if reseller and this is a B2B order (PayPal)
            if ($resellerId && $reseller && $user instanceof \App\Models\B2b && $createdPaypalOrder) {
                $commissionAmount = $request->total * 0.05;

                Commission::create([
                    'reseller_id' => $resellerId,
                    'order_id' => $createdPaypalOrder->id,
                    'amount' => $commissionAmount,
                    'status' => 'pending',
                ]);

                Log::info('Commission created for PayPal B2B order', [
                    'reseller_id' => $resellerId,
                    'order_id' => $order_id,
                    'commission_amount' => $commissionAmount
                ]);
            }

            // Send order confirmation email to customer and admin
            try {
                Mail::to($request->email)->send(new OrderMail($order_id));
                Log::info('Order confirmation email sent to customer');
                
                // Send copy to admin email from .env file
                $adminEmail = env('MAIL_USERNAME');
                if (!empty($adminEmail)) {
                    Mail::to($adminEmail)->send(new OrderMail($order_id));
                    Log::info('Order confirmation email sent to admin');
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send order confirmation email: ' . $e->getMessage());
            }

            return response()->json([
                'redirect' => url('paypal?order_id='.$order_id),
            ]);
        }
        else if($request->payment_method == '5'){
            Log::info('Processing payment method 5 (Apple Pay) - using apple_pay_orders table');

            // Validate required fields for Apple Pay (includes billing info)
            $requiredFields = ['sub_total', 'total', 'payment_method', 'name', 'street', 'city', 'phone', 'email'];
            foreach ($requiredFields as $field) {
                if ($request->$field === null || $request->$field === '') {
                    Log::error("Missing required field for Apple Pay: $field", ['request' => $request->all()]);
                    return response()->json([
                        'error' => "Missing required field: $field"
                    ], 400);
                }
            }

            // Ensure discount has a value
            $discount = $request->discount ?? 0;
            $coupon = $request->coupon ?? '';

            // Get cart items for Apple Pay orders
            $cartQuery = Cart::with('rel_to_product');
            if (isset($userData['coustomer_id'])) {
                $cartQuery->where('coustomer_id', $userData['coustomer_id']);
            } elseif (isset($userData['reseller_id'])) {
                $cartQuery->where('reseller_id', $userData['reseller_id']);
            } elseif (isset($userData['b2b_id'])) {
                $cartQuery->where('b2b_id', $userData['b2b_id']);
            } elseif (isset($userData['distributer_id'])) {
                $cartQuery->where('distributer_id', $userData['distributer_id']);
            } elseif (isset($userData['amazon_id'])) {
                $cartQuery->where('amazon_id', $userData['amazon_id']);
            }
            $carts = $cartQuery->get();

            // Calculate totals for orders table
            $total_quantity = $carts->sum('quantity');
            $total_sell_price = $carts->sum(function($cart) {
                return ($cart->sell_price ?? $cart->price ?? 0) * $cart->quantity;
            });
            $total_cost_price = $carts->sum(function($cart) {
                return ($cart->cost_price ?? 0) * $cart->quantity;
            });

            // Create order in orders table
            $orderData = array_merge([
                'order_id' => $order_id,
                'sub_total' => $request->sub_total,
                'total' => $request->total,
                'discount' => $discount,
                'payment_method' => (int)$request->payment_method,
                'coupon' => $coupon ?: null,
                'status' => 1, // Set default status to 1 (pending)
                'quantity' => $total_quantity,
                'sell_price' => $total_sell_price,
                'cost_price' => $total_cost_price,
                'delivery_charge' => $request->shipping_cost ?? 0,
            ], $userData);

            Order::create($orderData);
            Log::info('Order inserted in orders table for Apple Pay');

            // Create Apple Pay order - using apple_pay_orders table structure (includes billing fields)
            $applePayOrderData = array_merge([
                'order_id' => $order_id,
                'sub_total' => $request->sub_total,
                'total' => $request->total,
                'discount' => $discount,
                'delivery_charge' => $request->shipping_cost ?? 0,
                'payment_method' => (int)$request->payment_method,
                'coupon' => $coupon ?: null,
                'status' => 1, // Set default status to 1 (pending)
                'name' => $request->name,
                'company' => $request->company ?: null,
                'street' => $request->street,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'phone' => $request->phone,
                'email' => $request->email,
            ], $userData);

            Log::info('Apple Pay order data to insert:', $applePayOrderData);
            \App\Models\ApplePayOrder::insert($applePayOrderData);
            Log::info('Apple Pay order inserted successfully');

            // Create order products for Apple Pay orders
            foreach($carts as $cart){
                OrderProduct::create([
                    'order_id' => $order_id,
                    'product_id' => $cart->product_id,
                    'color_id' => $cart->color_id,
                    'size_id' => $cart->size_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->price ?? 0,
                    'sell_price' => $cart->sell_price ?? $cart->price ?? 0,
                    'cost_price' => $cart->cost_price ?? 0,
                ]);
            }
            Log::info('Order products inserted for Apple Pay order');

            // Get the created order for commission tracking
            $createdApplePayOrder = Order::where('order_id', $order_id)->first();

            // Create commission if reseller and this is a B2B order (Apple Pay)
            if ($resellerId && $reseller && $user instanceof \App\Models\B2b && $createdApplePayOrder) {
                $commissionAmount = $request->total * 0.05;

                Commission::create([
                    'reseller_id' => $resellerId,
                    'order_id' => $createdApplePayOrder->id,
                    'amount' => $commissionAmount,
                    'status' => 'pending',
                ]);

                Log::info('Commission created for Apple Pay B2B order', [
                    'reseller_id' => $resellerId,
                    'order_id' => $order_id,
                    'commission_amount' => $commissionAmount
                ]);
            }

            // Send order confirmation email to customer and admin
            try {
                Mail::to($request->email)->send(new OrderMail($order_id));
                Log::info('Order confirmation email sent to customer');
                
                // Send copy to admin email from .env file
                $adminEmail = env('MAIL_USERNAME');
                if (!empty($adminEmail)) {
                    Mail::to($adminEmail)->send(new OrderMail($order_id));
                    Log::info('Order confirmation email sent to admin');
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send order confirmation email: ' . $e->getMessage());
            }

            return response()->json([
                'redirect' => url('apple-pay?order_id='.$order_id),
            ]);
        }
        // QR Code Payments (Venmo QR = 7, Cash App QR = 8)
        else if(in_array($request->payment_method, ['7', '8', 7, 8])){
            Log::info('Processing QR payment method ' . $request->payment_method . ' - using orders table with pending payment status');
            
            // For QR payments, we create the order but mark payment as pending
            // Payment will be confirmed by admin after reviewing screenshot
            
            // Validate required fields for QR Payment
            $requiredFields = ['sub_total', 'total', 'payment_method', 'name', 'street', 'city', 'phone', 'email'];
            foreach ($requiredFields as $field) {
                if ($request->$field === null || $request->$field === '') {
                    Log::error("Missing required field for QR Payment: $field", ['request' => $request->all()]);
                    return response()->json([
                        'error' => "Missing required field: $field"
                    ], 400);
                }
            }
            
            // Ensure discount has a value
            $discount = $request->discount ?? 0;
            $coupon = $request->coupon ?? '';

            // Get cart items for the authenticated user
            $cartQuery = Cart::with('rel_to_product');
            if (isset($userData['coustomer_id'])) {
                $cartQuery->where('coustomer_id', $userData['coustomer_id']);
            } elseif (isset($userData['reseller_id'])) {
                $cartQuery->where('reseller_id', $userData['reseller_id']);
            } elseif (isset($userData['b2b_id'])) {
                $cartQuery->where('b2b_id', $userData['b2b_id']);
            } elseif (isset($userData['distributer_id'])) {
                $cartQuery->where('distributer_id', $userData['distributer_id']);
            } elseif (isset($userData['amazon_id'])) {
                $cartQuery->where('amazon_id', $userData['amazon_id']);
            }
            $carts = $cartQuery->get();
            Log::info('Retrieved carts for QR payment user', ['count' => $carts->count()]);

            // Calculate totals for orders table
            $total_quantity = $carts->sum('quantity');
            $total_sell_price = $carts->sum(function($cart) {
                return ($cart->sell_price ?? $cart->price ?? 0) * $cart->quantity;
            });
            $total_cost_price = $carts->sum(function($cart) {
                return ($cart->cost_price ?? 0) * $cart->quantity;
            });

            // Create order for QR Payment - using orders table structure with pending status
            $orderData = array_merge([
                'order_id' => $order_id,
                'sub_total' => $request->sub_total,
                'total' => $request->total,
                'balance_used' => $balanceUsed,
                'discount' => $discount,
                'payment_method' => (int)$request->payment_method,
                'coupon' => $coupon ?: null,
                'payment_status' => 'pending', // Payment pending admin approval
                'status' => 1, // Order pending until payment approved
                'quantity' => $total_quantity,
                'sell_price' => $total_sell_price,
                'cost_price' => $total_cost_price,
                'delivery_charge' => $request->shipping_cost ?? 0,
            ], $userData);
            
            Log::info('QR Payment order data to insert:', $orderData);
            
            // Create the order using Eloquent create method
            $createdOrder = Order::create($orderData);
            Log::info('QR Payment order created: ' . ($createdOrder ? 'SUCCESS' : 'FAILED'));

            if (!$createdOrder) {
                Log::error('Failed to create QR payment order', ['data' => $orderData]);
                return response()->json([
                    'error' => 'Failed to create order in database'
                ], 500);
            }
            
            // Create billing record for QR Payment orders
            $billingData = array_merge([
                'order_id' => $order_id,
                'name' => $request->name,
                'company' => $request->company ?: null,
                'street' => $request->street,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'phone' => $request->phone,
                'email' => $request->email,
            ], $userData);

            Log::info('Billing data to create for QR payment:', $billingData);
            $billingResult = Billing::create($billingData);
            Log::info('Billing create result for QR payment: ' . ($billingResult ? 'SUCCESS' : 'FAILED'));

            // Store cart items for inventory deduction BEFORE creating order products
            $cartItemsForInventory = $carts->map(function($cart) {
                return [
                    'product_id' => $cart->product_id,
                    'color_id' => $cart->color_id,
                    'size_id' => $cart->size_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->price ?? 0,
                ];
            })->toArray();

            foreach($carts as $cart){
                OrderProduct::create([
                    'order_id' => $order_id,
                    'product_id' => $cart->product_id,
                    'color_id' => $cart->color_id,
                    'size_id' => $cart->size_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->price ?? 0,
                    'sell_price' => $cart->sell_price ?? $cart->price ?? 0,
                    'cost_price' => $cart->cost_price ?? 0,
                ]);
            }
            Log::info('Order products inserted successfully for QR payment');

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
            Log::info('Cart cleared successfully for QR payment');

            Log::info('QR Payment order processing completed for order_id: ' . $order_id);
            
            return response()->json([
                'order_id' => $order_id,
                'message' => 'Order created successfully. Please complete your QR payment.'
            ]);
        }
        else {
            Log::error('Invalid payment method', ['payment_method' => $request->payment_method]);
            return response()->json([
                'error' => 'Invalid payment method'
            ], 400);
        }
        
    } catch (\Exception $e) {
        Log::error('Checkout error: ' . $e->getMessage(), [
            'exception' => $e,
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        
        return response()->json([
            'error' => 'An error occurred while processing your order: ' . $e->getMessage(),
        ], 500);
    }
}
}
