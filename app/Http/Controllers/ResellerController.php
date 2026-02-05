<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use App\Models\Commission;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class ResellerController extends Controller
{
    public function register(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|string',
        //     'reseller_email' => 'required|email|unique:resellers',
        //     'phone' => 'required|string',
        //     'venmo_zelle_id' => 'required|string',
        //     'password' => 'required|string|min:8',
        // ]);

        $referralCode = strtoupper(Str::random(8));                     
        $discountCode = 'RES' . strtoupper(Str::random(6));

        $reseller = Reseller::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'venmo_zelle_id' => $request->venmo_zelle_id ?? '',
            'password' => Hash::make($request->password),
            'unique_ref_id' => $referralCode,
            'discount_code' => $discountCode,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Reseller registered successfully', 'reseller' => $reseller], 201);
    }

    public function dashboard(Request $request)
    {
        $reseller = $request->user();

        // Ensure ref_link is set
        if (!$reseller->ref_link) {
            $reseller->ref_link = $reseller->unique_ref_id ?: strtoupper(Str::random(8));
            $reseller->save();
        }

        $commissions = $reseller->commissions()->selectRaw('status, SUM(amount) as total')->groupBy('status')->get();

        $pending = $commissions->where('status', 'pending')->first()->total ?? 0;
        $available = $commissions->where('status', 'available')->first()->total ?? 0;
        $paid = $commissions->where('status', 'paid')->first()->total ?? 0;
        $used = $commissions->where('status', 'used')->first()->total ?? 0;

        $referredB2bs = \App\Models\B2b::where('ref_id', $reseller->unique_ref_id)->count();
        $totalOrders = $reseller->orders()->count();
        $totalEarnings = $paid;

        // Get reseller's own orders
        $resellerOrders = $reseller->orders()
            ->with(['orderProducts.product'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get B2B orders through referrals with commission details
        $b2bOrders = \App\Models\Order::whereHas('b2b', function($q) use ($reseller) {
                $q->where('ref_id', $reseller->unique_ref_id);
            })
            ->with(['orderProducts.product', 'b2b', 'commissions' => function($q) use ($reseller) {
                $q->where('reseller_id', $reseller->id);
            }])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get detailed commission history
        $commissionHistory = $reseller->commissions()
            ->with(['order.orderProducts.product', 'order.b2b'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        // Get latest payout request
        $payoutRequest = PayoutRequest::where('reseller_id', $reseller->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'ref_link' => $reseller->ref_link,
            'commission_wallet' => [
                'pending' => $pending,
                'available' => $available,
                'paid' => $paid,
                'used' => $used,
            ],
            'activities' => [
                'referred_b2bs' => $referredB2bs,
                'total_orders' => $totalOrders,
                'total_earnings' => $totalEarnings,
            ],
            'reseller_orders' => $resellerOrders,
            'b2b_referral_orders' => $b2bOrders,
            'commission_history' => $commissionHistory,
            'payout_request' => $payoutRequest,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $reseller = Reseller::where('email', $request->email)->first();
        if (! $reseller || ! Hash::check($request->password, $reseller->password)) {
            return response()->json([
                'error' => 'The provided credentials are incorrect.',
            ], 401);
        }
        
        if ($reseller->status !== 'approved') {
            return response()->json(['message' => 'Account not approved yet'], 403);
        }
        
        // Create Sanctum token
        $token = $reseller->createToken('reseller-token')->plainTextToken;

        $resellerData = $reseller->toArray();
        $resellerData['user_type'] = 'reseller';

        return response()->json([
            'message' => 'Login successful',
            'reseller' => $resellerData,
            'token' => $token,
        ]);
    }

    public function requestPayout(Request $request)
    {
        $reseller = $request->user();

        // Check if reseller has any pending payout requests
        $existingRequest = PayoutRequest::where('reseller_id', $reseller->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json(['error' => 'You already have a pending payout request'], 400);
        }

        $commissionId = $request->input('commission_id');

        if ($commissionId) {
            // Request payout for specific commission
            $commission = $reseller->commissions()->find($commissionId);

            if (!$commission) {
                return response()->json(['error' => 'Commission not found'], 404);
            }

            if ($commission->status !== 'pending') {
                return response()->json(['error' => 'Commission is not pending for payout'], 400);
            }

            $amount = $commission->amount;

            // Create payout request for specific commission
            PayoutRequest::create([
                'reseller_id' => $reseller->id,
                'amount' => $amount,
                'status' => 'pending',
                'commission_id' => $commissionId, // Add commission_id to track which commission this payout is for
            ]);

            return response()->json(['message' => 'Payout request submitted successfully for this commission']);
        } else {
            // Request payout for all pending commissions
            $pendingAmount = $reseller->commissions()
                ->where('status', 'pending')
                ->sum('amount');

            if ($pendingAmount <= 0) {
                return response()->json(['error' => 'No pending commissions for payout'], 400);
            }

            // Create payout request for all pending commissions
            PayoutRequest::create([
                'reseller_id' => $reseller->id,
                'amount' => $pendingAmount,
                'status' => 'pending',
            ]);

            return response()->json(['message' => 'Payout request submitted successfully']);
        }
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $userData = $user->toArray();
        $userData['user_type'] = 'reseller';
        return response()->json($userData);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
