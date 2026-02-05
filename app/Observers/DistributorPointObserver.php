<?php

namespace App\Observers;

use App\Models\DistributorPoint;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\Log;

class DistributorPointObserver
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    /**
     * Handle the DistributorPoint "creating" event.
     * Automatically geocode before creating if lat/lng not provided.
     */
    public function creating(DistributorPoint $distributorPoint): void
    {
        $this->geocodeIfNeeded($distributorPoint);
    }

    /**
     * Handle the DistributorPoint "updating" event.
     * Automatically geocode if ZIP code changed and lat/lng not provided.
     */
    public function updating(DistributorPoint $distributorPoint): void
    {
        // Check if ZIP code changed
        if ($distributorPoint->isDirty('zip_code')) {
            // Reset lat/lng if ZIP changed
            $distributorPoint->lat = null;
            $distributorPoint->lng = null;
            $this->geocodeIfNeeded($distributorPoint);
        }
    }

    /**
     * Geocode the distributor point if lat/lng are missing.
     */
    private function geocodeIfNeeded(DistributorPoint $distributorPoint): void
    {
        // Only geocode if lat/lng are empty
        if (empty($distributorPoint->lat) || empty($distributorPoint->lng)) {
            if (!empty($distributorPoint->zip_code)) {
                Log::info("Auto-geocoding distributor: {$distributorPoint->name} (ZIP: {$distributorPoint->zip_code})");

                try {
                    $coordinates = $this->geocodingService->getCoordinates($distributorPoint->zip_code);

                    if ($coordinates) {
                        $distributorPoint->lat = $coordinates['lat'];
                        $distributorPoint->lng = $coordinates['lng'];
                        Log::info("Auto-geocoding successful: Lat={$coordinates['lat']}, Lng={$coordinates['lng']}");
                    } else {
                        Log::warning("Auto-geocoding failed for ZIP: {$distributorPoint->zip_code}");
                    }
                } catch (\Exception $e) {
                    Log::error("Auto-geocoding error: {$e->getMessage()}");
                }
            }
        }
    }
}
