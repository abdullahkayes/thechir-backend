<?php

namespace App\Http\Controllers;

use App\Models\Amazon;
use App\Models\PaymentUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class AmazonController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'business_name' => 'required|nullable|string',
                'business_email' => 'required|nullable|email',
                'business_phone' => 'required|nullable|string',
                'business_address' => 'required|nullable|string',
                'name' => 'required|string',
                'email' => 'required|email|unique:amazons',
                'password' => 'required|string|min:8',
                'amazon_seller_id' => 'nullable|string',
                'website' => 'nullable|url',
                'tax_id' => 'nullable|string',
                'terms_accepted' => 'required|boolean|accepted',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }

            $amazon = Amazon::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'business_name' => $request->business_name,
                'ein' => $request->tax_id ?? 'PENDING',
                'resale_certificate_path' => 'pending',
                'shipping_address' => $request->business_address,
                'status' => 'pending',
                'ref_id' => 'AMAZON-' . strtoupper(uniqid()),
                'amazon_seller_id' => $request->amazon_seller_id,
                'website' => $request->website,
            ]);

            // Create token for the new Amazon user
            $token = $amazon->createToken('amazon-token')->plainTextToken;

            return response()->json([
                'message' => 'Amazon registration submitted for approval',
                'amazon' => $amazon,
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $amazon = Amazon::where('email', $request->email)->first();
        if (! $amazon || ! Hash::check($request->password, $amazon->password)) {
            return response()->json([
                'error' => 'The provided credentials are incorrect.',
            ], 401);
        }
        
        if ($amazon->status !== 'approved') {
            return response()->json(['message' => 'Account not approved yet'], 403);
        }
        
        // Create Sanctum token
        $token = $amazon->createToken('amazon-token')->plainTextToken;

        $amazonData = $amazon->toArray();
        $amazonData['user_type'] = 'amazon';

        return response()->json([
            'message' => 'Login successful',
            'amazon' => $amazonData,
            'token' => $token,
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Get orders for this Amazon user with order products and product details including images
        $orders = \App\Models\Order::where('amazon_id', $user->id)
            ->with(['orderProducts.product.rel_to_gal'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'user' => $user,
            'orders' => $orders,
            'total_orders' => $orders->count(),
            'total_sales' => $orders->sum('total'),
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $userData = $user->toArray();
        $userData['user_type'] = 'amazon';
        return response()->json($userData);
    }

    public function paymentUpdate(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'integer|exists:orders,id'
        ]);

        $user = $request->user();

        foreach ($request->order_ids as $orderId) {
            // Check if order belongs to this amazon user and is pending
            $order = \App\Models\Order::where('id', $orderId)
                ->where('amazon_id', $user->id)
                ->where('payment_status', 'pending')
                ->first();

            if ($order) {
                PaymentUpdate::create([
                    'order_id' => $orderId,
                    'amazon_id' => $user->id,
                    'status' => 'pending'
                ]);
            }
        }

        return response()->json(['message' => 'Payment updates sent successfully']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}