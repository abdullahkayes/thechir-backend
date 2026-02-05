<?php

namespace App\Http\Controllers;

use App\Models\B2b;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class B2bController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string',
            'ein' => 'required|string',
            'resale_certificate' => 'required|file|mimes:pdf,jpg,png',
            'shipping_address' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email|unique:b2bs',
            'password' => 'required|string|min:8',
            'referral_code' => 'nullable|string|exists:resellers,unique_ref_id',
        ]);

        $referralCode = $request->referral_code;

        // Store file in public/upload/resale_certificates directory
        $file = $request->file('resale_certificate');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('upload/resale_certificates'), $filename);
        $path = 'upload/resale_certificates/' . $filename; // Store relative path in database

        $b2b = B2b::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'business_name' => $request->business_name,
            'ein' => $request->ein,
            'resale_certificate_path' => $path,
            'shipping_address' => $request->shipping_address,
            'ref_id' => $referralCode ?: null,
            'status' => 'pending',
        ]);

        if ($referralCode) {
            Cookie::queue('ref_id', $referralCode, 60*24*30); // 30 days
        }

        return response()->json(['message' => 'B2B registration submitted for approval', 'b2b' => $b2b], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $b2b = B2b::where('email', $request->email)->first();
        if (! $b2b || ! Hash::check($request->password, $b2b->password)) {
            return response()->json([
                'error' => 'The provided credentials are incorrect.',
            ], 401);
        }
        
        if ($b2b->status !== 'approved') {
            return response()->json(['message' => 'Account not approved yet'], 403);
        }
        
        // Create Sanctum token
        $token = $b2b->createToken('b2b-token')->plainTextToken;

        $b2bData = $b2b->toArray();
        $b2bData['user_type'] = 'b2b';

        return response()->json([
            'message' => 'Login successful',
            'b2b' => $b2bData,
            'token' => $token,
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Get orders for this B2B user with order products and product details including images
        $orders = \App\Models\Order::where('b2b_id', $user->id)
            ->with(['orderProducts.product.rel_to_gal'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'user' => $user,
            'orders' => $orders,
            'total_orders' => $orders->count(),
            'total_spent' => $orders->sum('total'),
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $userData = $user->toArray();
        $userData['user_type'] = 'b2b';
        return response()->json($userData);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
