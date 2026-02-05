<?php

namespace App\Services;

class DistanceService
{
    /**
     * Earth's radius in miles.
     */
    private const EARTH_RADIUS_MILES = 3959;

    /**
     * Calculate distance between two coordinates using Haversine formula.
     * Returns distance in miles.
     * 
     * @param float $lat1 Origin latitude
     * @param float $lon1 Origin longitude
     * @param float $lat2 Destination latitude
     * @param float $lon2 Destination longitude
     * @return float Distance in miles
     */
    public function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_MILES * $c;
    }

    /**
     * Calculate distance between two coordinate arrays.
     * 
     * @param array $coord1 ['lat' => float, 'lng' => float]
     * @param array $coord2 ['lat' => float, 'lng' => float]
     * @return float Distance in miles
     */
    public function calculateDistanceBetweenCoords(array $coord1, array $coord2): float
    {
        return $this->calculateDistance(
            $coord1['lat'],
            $coord1['lng'],
            $coord2['lat'],
            $coord2['lng']
        );
    }

    /**
     * Calculate distance in kilometers.
     * 
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distance in kilometers
     */
    public function calculateDistanceKm(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        // Earth's radius in km
        $earthRadiusKm = 6371;
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }

    /**
     * Get distance zone based on miles.
     * 
     * @param float $miles
     * @return string Zone name: 'local', 'regional', 'national', or 'extended'
     */
    public function getDistanceZone(float $miles): string
    {
        if ($miles <= 150) {
            return 'local';
        } elseif ($miles <= 600) {
            return 'regional';
        } elseif ($miles <= 1400) {
            return 'national';
        } else {
            return 'extended';
        }
    }

    /**
     * Get zone multiplier for shipping calculations.
     * 
     * @param float $miles
     * @return float Multiplier value
     */
    public function getZoneMultiplier(float $miles): float
    {
        $zone = $this->getDistanceZone($miles);
        
        return match ($zone) {
            'local' => 1.0,
            'regional' => 1.15,
            'national' => 1.30,
            'extended' => 1.50,
            default => 1.0,
        };
    }
}
