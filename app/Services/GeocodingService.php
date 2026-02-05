<?php

namespace App\Services;

use App\Models\ZipCoordinate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    private string $locationIqApiKey;
    private string $googleMapsApiKey;

    public function __construct()
    {
        $this->locationIqApiKey = env('LOCATIONIQ_API_KEY', '');
        $this->googleMapsApiKey = env('GOOGLE_MAPS_API_KEY', '');
    }

    /**
     * Get latitude/longitude for a ZIP code.
     * Uses cache first, then falls back to geocoding APIs.
     * 
     * @param string $zip
     * @return array ['lat' => float, 'lng' => float]|null
     */
    public function getCoordinates(string $zip): ?array
    {
        // Normalize ZIP
        $zip = $this->normalizeZip($zip);

        // Check cache first (permanent cache - never expires)
        try {
            $cached = ZipCoordinate::where('zip', $zip)->first();
            if ($cached) {
                Log::debug("GeocodingService: ZIP $zip found in cache", [
                    'lat' => $cached->latitude,
                    'lng' => $cached->longitude
                ]);
                return [
                    'lat' => (float) $cached->latitude,
                    'lng' => (float) $cached->longitude
                ];
            }
        } catch (\Exception $e) {
            Log::warning("GeocodingService: Error querying zip_coordinates table: " . $e->getMessage());
            // Continue to API calls even if cache query fails
        }

        // Not cached - call APIs
        Log::debug("GeocodingService: ZIP $zip not in cache, calling APIs");

        // Try LocationIQ first (primary)
        $coordinates = $this->tryLocationIQ($zip);
        if ($coordinates) {
            $this->cacheZip($zip, $coordinates);
            return $coordinates;
        }

        // Fallback to Google Maps
        $coordinates = $this->tryGoogleMaps($zip);
        if ($coordinates) {
            $this->cacheZip($zip, $coordinates);
            return $coordinates;
        }

        Log::error("GeocodingService: All geocoding services failed for ZIP $zip");
        return null;
    }

    /**
     * Get coordinates for a full address (city, state, zip).
     * Used for distributor points.
     * 
     * @param string $address
     * @return array|null
     */
    public function getCoordinatesForAddress(string $address): ?array
    {
        // Try LocationIQ first
        $coordinates = $this->tryLocationIQAddress($address);
        if ($coordinates) {
            return $coordinates;
        }

        // Fallback to Google Maps
        $coordinates = $this->tryGoogleMapsAddress($address);
        if ($coordinates) {
            return $coordinates;
        }

        Log::error("GeocodingService: All geocoding services failed for address: $address");
        return null;
    }

    /**
     * Try LocationIQ for ZIP code geocoding.
     */
    private function tryLocationIQ(string $zip): ?array
    {
        if (empty($this->locationIqApiKey)) {
            Log::debug('GeocodingService: LocationIQ API key not configured');
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->get("https://us1.locationiq.com/v1/search.php", [
                    'key' => $this->locationIqApiKey,
                    'q' => $zip . ', USA',
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'us'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                    Log::debug("GeocodingService: LocationIQ success for ZIP $zip");
                    return [
                        'lat' => (float) $data[0]['lat'],
                        'lng' => (float) $data[0]['lon']
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("GeocodingService: LocationIQ error for ZIP $zip: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Try Google Maps for ZIP code geocoding.
     */
    private function tryGoogleMaps(string $zip): ?array
    {
        if (empty($this->googleMapsApiKey) || $this->googleMapsApiKey === 'YOUR_GOOGLE_MAPS_API_KEY') {
            Log::debug('GeocodingService: Google Maps API key not configured');
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->get("https://maps.googleapis.com/maps/api/geocode/json", [
                    'address' => $zip . ', USA',
                    'key' => $this->googleMapsApiKey
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    $location = $data['results'][0]['geometry']['location'];
                    Log::debug("GeocodingService: Google Maps success for ZIP $zip");
                    return [
                        'lat' => (float) $location['lat'],
                        'lng' => (float) $location['lng']
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("GeocodingService: Google Maps error for ZIP $zip: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Try LocationIQ for full address geocoding.
     */
    private function tryLocationIQAddress(string $address): ?array
    {
        if (empty($this->locationIqApiKey)) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->get("https://us1.locationiq.com/v1/search.php", [
                    'key' => $this->locationIqApiKey,
                    'q' => $address . ', USA',
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'us'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                    return [
                        'lat' => (float) $data[0]['lat'],
                        'lng' => (float) $data[0]['lon']
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("GeocodingService: LocationIQ address error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Try Google Maps for full address geocoding.
     */
    private function tryGoogleMapsAddress(string $address): ?array
    {
        if (empty($this->googleMapsApiKey) || $this->googleMapsApiKey === 'YOUR_GOOGLE_MAPS_API_KEY') {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->get("https://maps.googleapis.com/maps/api/geocode/json", [
                    'address' => $address . ', USA',
                    'key' => $this->googleMapsApiKey
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    $location = $data['results'][0]['geometry']['location'];
                    return [
                        'lat' => (float) $location['lat'],
                        'lng' => (float) $location['lng']
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("GeocodingService: Google Maps address error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Cache ZIP coordinates permanently in database.
     */
    private function cacheZip(string $zip, array $coordinates): void
    {
        try {
            // Use raw query to avoid Eloquent issues with string primary keys
            \DB::table('zip_coordinates')->updateOrInsert(
                ['zip' => $zip],
                [
                    'latitude' => $coordinates['lat'],
                    'longitude' => $coordinates['lng'],
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
            Log::debug("GeocodingService: Cached ZIP $zip");
        } catch (\Exception $e) {
            Log::warning("GeocodingService: Failed to cache ZIP $zip: " . $e->getMessage());
            // Non-critical error, continue without caching
        }
    }

    /**
     * Normalize ZIP code.
     */
    private function normalizeZip(string $zip): string
    {
        // Remove any non-numeric characters except for the optional +4 part
        $zip = preg_replace('/[^0-9-]/', '', $zip);
        // Take only first 5 digits for standardization
        $zip = substr($zip, 0, 5);
        return $zip;
    }
}
