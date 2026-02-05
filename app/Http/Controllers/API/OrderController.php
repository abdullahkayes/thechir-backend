<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
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
                    $user instanceof \App\Models\B2b || $user instanceof \App\Models\Distributer) {
                    return $user;
                }
            }
        }

        return null;
    }


    function myorder($id){
        // Detect user type from authenticated user
        $user = $this->getAuthenticatedUser();
        if (!$user) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $query = Order::query();

        // Filter by user type
        if ($user instanceof \App\Models\Coustomer || $user instanceof \App\Models\User) {
            $query->where('coustomer_id', $user->id);
        } elseif ($user instanceof \App\Models\Reseller) {
            $query->where('reseller_id', $user->id);
        } elseif ($user instanceof \App\Models\B2b) {
            $query->where('b2b_id', $user->id);
        } elseif ($user instanceof \App\Models\Distributer) {
            $query->where('distributer_id', $user->id);
        } else {
            return response()->json(['error' => 'Invalid user type'], 403);
        }

        $myorders = $query->get();
        return response()->json([
            'myorders' => $myorders
        ]);
    }

    function invoice($id){
        $data =Order::find($id);
       $pdf = Pdf::loadView('pdf.invoice',[
        'data'=>$data,
       ]);
      return $pdf->download('invoice.pdf');
    }
    
    function orderDetails($order_id){
        $order = Order::where('order_id', $order_id)->first();
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
        
        // Get order products
        $orderProducts = \App\Models\OrderProduct::where('order_id', $order_id)->with('rel_to_product')->get();
        
        // Format order details for frontend
        $formattedOrder = [
            'order_id' => $order->order_id,
            'total' => $order->total,
            'sub_total' => $order->sub_total,
            'discount' => $order->discount,
            'payment_method' => $this->getPaymentMethodName($order->payment_method),
            'shipping_method' => 'Standard', // Default, can be extended with actual data
            'status' => $order->status,
            'created_at' => $order->created_at,
            'products' => $orderProducts->map(function($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->rel_to_product->product_name,
                    'quantity' => $item->quantity,
                    'price' => $item->price
                ];
            })
        ];
        
        return response()->json([
            'success' => true,
            'data' => $formattedOrder
        ]);
    }
    
    /**
     * Helper function to get payment method name
     */
    private function getPaymentMethodName($paymentMethod) {
        $methods = [
            1 => 'Cash on Delivery',
            2 => 'Stripe',
            3 => 'PayPal',
            5 => 'Apple Pay',
            7 => 'Venmo',
            8 => 'Cash App'
        ];
        
        return $methods[$paymentMethod] ?? 'Unknown';
    }}
