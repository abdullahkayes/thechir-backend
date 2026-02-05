<?php

namespace App\Services;

use App\Models\DistributorPoint;

class DistributorSelectorService
{
    private DistanceService $distanceService;

    public function __construct(DistanceService $distanceService)
    {
        $this->distanceService = $distanceService;
    }

    /**
     * Find the nearest active distributor point to customer location.
     * 
     * @param float $customerLat Customer latitude
     * @param float $customerLng Customer longitude
     * @return array ['distributor' => DistributorPoint, 'distance' => float]|null
     */
    public function findNearestDistributor(float $customerLat, float $customerLng): ?array
    {
        $distributors = DistributorPoint::active()->get();

        if ($distributors->isEmpty()) {
            return null;
        }

        $nearestDistributor = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($distributors as $distributor) {
            // Get lat/lng - handle both accessor names and direct column names
            $lat = $distributor->lat ?? $distributor->latitude ?? null;
            $lng = $distributor->lng ?? $distributor->longitude ?? null;

            // Skip distributors without valid coordinates
            if (empty($lat) || empty($lng)) {
                continue;
            }

            $distance = $this->distanceService->calculateDistance(
                $customerLat,
                $customerLng,
                (float) $lat,
                (float) $lng
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestDistributor = $distributor;
            }
        }

        if ($nearestDistributor) {
            return [
                'distributor' => $nearestDistributor,
                'distance' => $minDistance
            ];
        }

        return null;
    }

    /**
     * Get the ZIP code of the nearest distributor.
     * 
     * @param float $customerLat
     * @param float $customerLng
     * @return string|null
     */
    public function getNearestDistributorZip(float $customerLat, float $customerLng): ?string
    {
        $result = $this->findNearestDistributor($customerLat, $customerLng);
        return $result['distributor']->zip_code ?? null;
    }

    /**
     * Get all distributors sorted by distance from customer location.
     * 
     * @param float $customerLat
     * @param float $customerLng
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDistributorsByDistance(float $customerLat, float $customerLng)
    {
        $distributors = DistributorPoint::active()->get();

        return $distributors->map(function ($distributor) {
            $distance = $this->distanceService->calculateDistance(
                $customerLat,
                $customerLng,
                (float) $distributor->latitude,
                (float) $distributor->longitude
            );
            
            return [
                'distributor' => $distributor,
                'distance' => $distance
            ];
        })->sortBy('distance')->values();
    }
}
