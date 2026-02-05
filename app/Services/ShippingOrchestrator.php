<?php

namespace App\Services;

use App\Services\Carriers\USPSService;
use App\Services\Carriers\FedExService;
use App\Services\Carriers\UPSService;
use App\Services\FallbackShippingService;
use Illuminate\Support\Facades\Log;

class ShippingOrchestrator
{
    private GeocodingService $geocodingService;
    private DistributorSelectorService $distributorSelector;
    private USPSService $uspsService;
    private FedExService $fedexService;
    private UPSService $upsService;
    private FallbackShippingService $fallbackService;
    private DistanceService $distanceService;

    public function __construct(
        GeocodingService $geocodingService,
        DistributorSelectorService $distributorSelector,
        USPSService $uspsService,
        FedExService $fedexService,
        UPSService $upsService,
        FallbackShippingService $fallbackService,
        DistanceService $distanceService
    ) {
        $this->geocodingService = $geocodingService;
        $this->distributorSelector = $distributorSelector;
        $this->uspsService = $uspsService;
        $this->fedexService = $fedexService;
        $this->upsService = $upsService;
        $this->fallbackService = $fallbackService;
        $this->distanceService = $distanceService;
    }

    /**
     * Calculate shipping cost for an order.
     * 
     * @param string $customerZip Customer's ZIP code
     * @param float $totalWeightLbs Total cart weight in pounds
     * @param array $cartItems Cart items with product info (optional, for validation)
     * @return array Shipping calculation result
     */
    public function calculateShipping(
        string $customerZip,
        float $totalWeightLbs,
        array $cartItems = []
    ): array {
        Log::info('ShippingOrchestrator: Starting calculation', [
            'customer_zip' => $customerZip,
            'weight_lbs' => $totalWeightLbs,
            'cart_items_count' => count($cartItems)
        ]);

        // Step 1: Calculate billable weight (minimum 1 lb, round up)
        $billableWeight = max(1, ceil($totalWeightLbs));

        Log::info('ShippingOrchestrator: Billable weight calculated', [
            'total_weight_lbs' => $totalWeightLbs,
            'billable_weight' => $billableWeight,
            'customer_zip' => $customerZip
        ]);

        // Step 2: Get customer coordinates from ZIP
        $customerCoords = $this->geocodingService->getCoordinates($customerZip);
        if (!$customerCoords) {
            Log::warning('ShippingOrchestrator: Could not geocode customer ZIP, using fallback', [
                'customer_zip' => $customerZip
            ]);
            $fallbackRate = $this->fallbackService->getRate($billableWeight, 0);
            return array_merge($fallbackRate, [
                'origin_zip' => '00000',
                'destination_zip' => $customerZip,
                'distance_miles' => 0,
                'rate_source' => 'fallback_no_geocode'
            ]);
        }

        Log::info('ShippingOrchestrator: Customer coordinates found', [
            'customer_zip' => $customerZip,
            'lat' => $customerCoords['lat'],
            'lng' => $customerCoords['lng']
        ]);

        // Step 3: Find nearest distributor
        $nearestDistributor = $this->distributorSelector->findNearestDistributor(
            $customerCoords['lat'],
            $customerCoords['lng']
        );

        if (!$nearestDistributor) {
            Log::warning('ShippingOrchestrator: No distributors found, using fallback', [
                'customer_zip' => $customerZip
            ]);
            $fallbackRate = $this->fallbackService->getRate($billableWeight, 0);
            return array_merge($fallbackRate, [
                'origin_zip' => '00000',
                'destination_zip' => $customerZip,
                'distance_miles' => 0,
                'rate_source' => 'fallback_no_distributor'
            ]);
        }

        $distributor = $nearestDistributor['distributor'];
        $distance = $nearestDistributor['distance'];

        // Ensure distributor has a valid ZIP code
        $originZip = $distributor->zip_code ?? null;
        if (empty($originZip)) {
            Log::warning('ShippingOrchestrator: Distributor has no ZIP code, using fallback', [
                'distributor_name' => $distributor->name ?? 'Unknown'
            ]);
            $fallbackRate = $this->fallbackService->getRate($billableWeight, $distance);
            return array_merge($fallbackRate, [
                'distributor' => $distributor->name ?? 'Unknown',
                'origin_zip' => '00000',
                'destination_zip' => $customerZip,
                'distance_miles' => round($distance, 2),
                'rate_source' => 'fallback_no_zip'
            ]);
        }

        Log::info('ShippingOrchestrator: Found nearest distributor', [
            'distributor' => $distributor->name,
            'origin_zip' => $originZip,
            'destination_zip' => $customerZip,
            'distance_miles' => $distance,
            'billable_weight' => $billableWeight
        ]);

        // Step 4: Try live carrier rates (FedEx first, then UPS, then USPS)
        $liveRate = $this->tryLiveCarrierRates(
            $originZip,
            $customerZip,
            $billableWeight
        );

        if ($liveRate) {
            Log::info('ShippingOrchestrator: Using live carrier rate', [
                'carrier' => $liveRate['carrier'] ?? 'Unknown',
                'cost' => $liveRate['cost'] ?? 0,
                'origin_zip' => $originZip,
                'destination_zip' => $customerZip,
                'distance_miles' => round($distance, 2)
            ]);
            return array_merge($liveRate, [
                'distributor' => $distributor->name,
                'origin_zip' => $originZip,
                'destination_zip' => $customerZip,
                'distance_miles' => round($distance, 2)
            ]);
        }

        // Step 5: Fall back to weight slab rates
        Log::info('ShippingOrchestrator: Live rates unavailable, using fallback', [
            'origin_zip' => $originZip,
            'destination_zip' => $customerZip,
            'billable_weight' => $billableWeight,
            'distance_miles' => $distance
        ]);
        $fallbackRate = $this->fallbackService->getRate($billableWeight, $distance);

        return array_merge($fallbackRate, [
            'distributor' => $distributor->name,
            'origin_zip' => $originZip,
            'destination_zip' => $customerZip,
            'distance_miles' => round($distance, 2)
        ]);
    }

    /**
     * Try to get live rates from all carriers (UPS → FedEx → USPS).
     * Returns the first successful rate found.
     * Priority: UPS (cheapest) → FedEx → USPS
     * 
     * @param string $originZip
     * @param string $destZip
     * @param float $weightLbs
     * @return array|null
     */
    private function tryLiveCarrierRates(
        string $originZip,
        string $destZip,
        float $weightLbs
    ): ?array {
        // Try UPS first (usually cheapest)
        Log::debug('ShippingOrchestrator: Trying UPS first...');
        $upsRate = $this->upsService->getGroundRate($originZip, $destZip, $weightLbs);
        
        Log::info('ShippingOrchestrator: UPS rate result', [
            'upsRate' => $upsRate,
            'originZip' => $originZip,
            'destZip' => $destZip,
            'weightLbs' => $weightLbs
        ]);
        
        if ($upsRate !== null) {
            Log::info('ShippingOrchestrator: UPS rate found', ['rate' => $upsRate]);
            return [
                'cost' => $upsRate,
                'carrier' => 'UPS',
                'service' => 'UPS Ground',
                'billable_weight' => $weightLbs,
                'rate_source' => 'live'
            ];
        }

        // Try FedEx second
        Log::debug('ShippingOrchestrator: UPS failed, trying FedEx...');
        $fedexRate = $this->fedexService->getGroundRate($originZip, $destZip, $weightLbs);
        
        Log::info('ShippingOrchestrator: FedEx rate result', [
            'fedexRate' => $fedexRate,
            'originZip' => $originZip,
            'destZip' => $destZip,
            'weightLbs' => $weightLbs
        ]);
        
        if ($fedexRate !== null) {
            Log::info('ShippingOrchestrator: FedEx rate found', ['rate' => $fedexRate]);
            return [
                'cost' => $fedexRate,
                'carrier' => 'FedEx',
                'service' => 'FedEx Ground',
                'billable_weight' => $weightLbs,
                'rate_source' => 'live'
            ];
        }

        // Try USPS last
        Log::debug('ShippingOrchestrator: FedEx failed, trying USPS...');
        $uspsRate = $this->uspsService->getGroundAdvantageRate($originZip, $destZip, $weightLbs);
        
        Log::info('ShippingOrchestrator: USPS rate result', [
            'uspsRate' => $uspsRate,
            'originZip' => $originZip,
            'destZip' => $destZip,
            'weightLbs' => $weightLbs
        ]);
        
        if ($uspsRate !== null) {
            Log::info('ShippingOrchestrator: USPS rate found', ['rate' => $uspsRate]);
            return [
                'cost' => $uspsRate,
                'carrier' => 'USPS',
                'service' => 'USPS Ground Advantage',
                'billable_weight' => $weightLbs,
                'rate_source' => 'live'
            ];
        }

        // All carriers failed - return null to trigger fallback
        Log::warning('ShippingOrchestrator: All carrier APIs failed');
        return null;
    }

    /**
     * Validate cart weight and get billable weight.
     * 
     * @param array $cartItems
     * @return float
     */
    public function calculateCartWeight(array $cartItems): float
    {
        $totalGrams = 0;

        foreach ($cartItems as $item) {
            // Get weight from cart item (in grams) - weight_grams is now total weight (product weight * quantity)
            $weightGrams = $item['weight_grams'] ?? null;

            // If weight_grams is not set, try to get from product and multiply by quantity
            if ($weightGrams === null) {
                $productWeight = $item['rel_to_product']['weight_grams'] ?? 500; // Default 500g per item
                $weightGrams = $productWeight * ($item['quantity'] ?? 1);
            }

            // Ensure weight is valid (not null, not negative)
            if ($weightGrams === null || $weightGrams < 0) {
                $weightGrams = 500 * ($item['quantity'] ?? 1);
            }

            $totalGrams += $weightGrams;

            Log::debug('ShippingOrchestrator: Item weight', [
                'product_id' => $item['product_id'] ?? 'unknown',
                'quantity' => $item['quantity'] ?? 0,
                'weight_grams' => $weightGrams,
                'total_item_grams' => $weightGrams
            ]);
        }

        // Convert grams to pounds
        $totalLbs = $totalGrams / 453.592;

        Log::info('ShippingOrchestrator: Cart weight calculated', [
            'total_grams' => $totalGrams,
            'total_lbs' => $totalLbs,
            'items_count' => count($cartItems)
        ]);

        return $totalLbs;
    }

    /**
     * Get shipping options (all available rates).
     * 
     * @param string $customerZip
     * @param float $totalWeightLbs
     * @return array
     */
    public function getShippingOptions(
        string $customerZip,
        float $totalWeightLbs
    ): array {
        $billableWeight = max(1, ceil($totalWeightLbs));

        // Get customer coordinates
        $customerCoords = $this->geocodingService->getCoordinates($customerZip);
        $distance = 0;

        if ($customerCoords) {
            $nearestDistributor = $this->distributorSelector->findNearestDistributor(
                $customerCoords['lat'],
                $customerCoords['lng']
            );
            
            if ($nearestDistributor) {
                $distance = $nearestDistributor['distance'];
            }
        }

        $options = [];

        // Try USPS
        if ($customerCoords && $nearestDistributor) {
            $uspsRates = $this->uspsService->getRates(
                $nearestDistributor['distributor']->zip_code,
                $customerZip,
                $billableWeight
            );
            
            if ($uspsRates) {
                foreach ($uspsRates as $rate) {
                    $options[] = [
                        'carrier' => 'USPS',
                        'service' => $rate['service'],
                        'cost' => $rate['rate'],
                        'billable_weight' => $billableWeight,
                        'rate_source' => 'live'
                    ];
                }
            }
        }

        // Add fallback option
        $fallback = $this->fallbackService->getRate($billableWeight, $distance);
        $options[] = [
            'carrier' => 'Standard',
            'service' => $fallback['service'],
            'cost' => $fallback['cost'],
            'billable_weight' => $billableWeight,
            'rate_source' => 'fallback'
        ];

        return $options;
    }
}
