<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ShippingOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    private ShippingOrchestrator $shippingOrchestrator;

    public function __construct(ShippingOrchestrator $shippingOrchestrator)
    {
        $this->shippingOrchestrator = $shippingOrchestrator;
    }

    /**
     * Calculate shipping cost for a given ZIP and cart.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'zip_code' => 'required|string|size:5',
            'cart' => 'required|array'
        ]);

        $zipCode = $request->zip_code;
        $cartItems = $request->cart;

        Log::info('ShippingController: Calculating shipping', [
            'zip_code' => $zipCode,
            'items_count' => count($cartItems),
            'cart_items' => array_map(function($item) {
                return [
                    'product_id' => $item['product_id'] ?? 'N/A',
                    'quantity' => $item['quantity'] ?? 0,
                    'weight_grams' => $item['weight_grams'] ?? 'not set',
                    'has_rel_product' => isset($item['rel_to_product']),
                    'rel_product_weight' => $item['rel_to_product']['weight_grams'] ?? 'not set'
                ];
            }, $cartItems)
        ]);

        try {
            // Calculate total weight from cart
            $totalWeightLbs = $this->shippingOrchestrator->calculateCartWeight($cartItems);

            Log::info('ShippingController: Weight calculated', [
                'total_weight_lbs' => $totalWeightLbs,
                'zip_code' => $zipCode
            ]);

            // Get shipping calculation
            $shippingResult = $this->shippingOrchestrator->calculateShipping(
                $zipCode,
                $totalWeightLbs,
                $cartItems
            );

            Log::info('ShippingController: Calculation complete', $shippingResult);

            return response()->json([
                'success' => true,
                'shipping' => $shippingResult,
                'billable_weight' => $shippingResult['billable_weight'] ?? max(1, ceil($totalWeightLbs))
            ]);

        } catch (\Exception $e) {
            Log::error('ShippingController: Error calculating shipping', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return fallback rate on error
            $fallbackWeight = max(1, ceil($this->shippingOrchestrator->calculateCartWeight($cartItems)));
            $fallback = [
                'cost' => $this->getDefaultFallbackRate($fallbackWeight),
                'service' => 'Standard Shipping',
                'carrier' => 'Standard',
                'billable_weight' => $fallbackWeight,
                'rate_source' => 'fallback_error',
                'error' => 'Live rates unavailable - ' . $e->getMessage()
            ];

            return response()->json([
                'success' => true,
                'shipping' => $fallback
            ]);
        }
    }

    /**
     * Get available shipping options for a given ZIP and cart.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShippingOptions(Request $request)
    {
        $request->validate([
            'zip_code' => 'required|string|size:5',
            'cart' => 'required|array'
        ]);

        $zipCode = $request->zip_code;
        $cartItems = $request->cart;

        try {
            $totalWeightLbs = $this->shippingOrchestrator->calculateCartWeight($cartItems);
            $options = $this->shippingOrchestrator->getShippingOptions($zipCode, $totalWeightLbs);

            return response()->json([
                'success' => true,
                'options' => $options
            ]);

        } catch (\Exception $e) {
            Log::error('ShippingController: Error getting options', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to retrieve shipping options'
            ], 500);
        }
    }

    /**
     * Get default fallback rate for emergency situations.
     */
    private function getDefaultFallbackRate(float $weightLbs): float
    {
        $weight = max(1, ceil($weightLbs));

        if ($weight <= 1) return 5.95;
        if ($weight <= 2) return 7.95;
        if ($weight <= 3) return 9.95;
        if ($weight <= 5) return 12.95;
        if ($weight <= 10) return 18.95;
        
        return 18.95 + (($weight - 10) * 1.50);
    }
}
