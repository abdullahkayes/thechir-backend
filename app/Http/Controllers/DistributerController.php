<?php

namespace App\Http\Controllers;

use App\Models\Distributer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DistributerController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:distributers',
            'license_number' => 'required|string',
            'company_name' => 'required|string',
            'address' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        $distributer = Distributer::create([
            'name' => $request->name,
            'email' => $request->email,
            'license_number' => $request->license_number,
            'company_name' => $request->company_name,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Distributer registered successfully', 'distributer' => $distributer], 201);
    }

    public function dashboard(Request $request)
    {
        $distributer = $request->user();

        $totalOrders = $distributer->orders()->count();
        $totalEarnings = $distributer->orders()->sum('total');

        // Get recent orders for the distributer
        $orders = $distributer->orders()
            ->orderBy('created_at', 'desc')
            ->take(10) // Limit to recent 10 orders
            ->get(['id', 'order_id', 'total', 'status', 'created_at']);

        return response()->json([
            'activities' => [
                'total_orders' => $totalOrders,
                'total_earnings' => $totalEarnings,
            ],
            'orders' => $orders,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $distributer = Distributer::where('email', $request->email)->first();
        if (! $distributer || ! Hash::check($request->password, $distributer->password)) {
            return response()->json([
                'error' => 'The provided credentials are incorrect.',
            ], 401);
        }

        if ($distributer->status !== 'approved') {
            return response()->json(['message' => 'Account not approved yet'], 403);
        }

        // Create Sanctum token
        $token = $distributer->createToken('distributer-token')->plainTextToken;

        $distributerData = $distributer->toArray();
        $distributerData['user_type'] = 'distributer';

        return response()->json([
            'message' => 'Login successful',
            'distributer' => $distributerData,
            'token' => $token,
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $userData = $user->toArray();
        $userData['user_type'] = 'distributer';
        return response()->json($userData);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}