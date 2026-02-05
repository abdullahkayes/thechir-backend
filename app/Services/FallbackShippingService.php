<?php

namespace App\Services;

class FallbackShippingService
{
    /**
     * Fallback rate table (DOMESTIC USA).
     * Used when live carrier APIs fail.
     * 
     * @param float $weightLbs Billable weight in pounds
     * @param float $distanceMiles Distance in miles (for zone-based pricing)
     * @return array ['cost' => float, 'service' => string]
     */
    public function getRate(float $weightLbs, float $distanceMiles = 0): array
    {
        $billableWeight = max(1, ceil($weightLbs)); // Minimum 1 lb

        // Base rates by weight slab
        $baseRate = $this->getBaseRate($billableWeight);
        
        // Apply distance multiplier if distance is provided
        $multiplier = $this->getDistanceMultiplier($distanceMiles);
        
        // Calculate final cost
        $cost = $baseRate * $multiplier;
        
        // Round to 2 decimal places
        $cost = round($cost, 2);

        return [
            'cost' => $cost,
            'service' => 'Standard Shipping',
            'carrier' => 'Standard',
            'billable_weight' => $billableWeight
        ];
    }

    /**
     * Get base rate for weight.
     * 
     * @param int $weightLbs
     * @return float
     */
    private function getBaseRate(int $weightLbs): float
    {
        // Rate table from prompt
        if ($weightLbs <= 1) {
            return 5.95;
        } elseif ($weightLbs <= 2) {
            return 7.95;
        } elseif ($weightLbs <= 3) {
            return 9.95;
        } elseif ($weightLbs <= 5) {
            return 12.95;
        } elseif ($weightLbs <= 10) {
            return 18.95;
        } else {
            // Over 10 lbs: $18.95 + $1.50 per extra lb
            $extraLbs = $weightLbs - 10;
            return 18.95 + ($extraLbs * 1.50);
        }
    }

    /**
     * Get distance multiplier for zone-based pricing.
     * 
     * @param float $miles
     * @return float
     */
    private function getDistanceMultiplier(float $miles): float
    {
        if ($miles <= 0) {
            return 1.0;
        }

        if ($miles <= 150) {
            return 1.0; // Local
        } elseif ($miles <= 600) {
            return 1.05; // Regional
        } elseif ($miles <= 1400) {
            return 1.10; // National
        } else {
            return 1.15; // Extended
        }
    }

    /**
     * Get all available fallback rates.
     * 
     * @param float $weightLbs
     * @param float $distanceMiles
     * @return array
     */
    public function getAllRates(float $weightLbs, float $distanceMiles = 0): array
    {
        $billableWeight = max(1, ceil($weightLbs));
        
        return [
            [
                'service' => 'Standard Shipping',
                'carrier' => 'Standard',
                'cost' => $this->getRate($weightLbs, $distanceMiles)['cost'],
                'billable_weight' => $billableWeight
            ]
        ];
    }
}
