<?php

namespace App\Console\Commands;

use App\Models\DistributorPoint;
use App\Services\GeocodingService;
use Illuminate\Console\Command;

class GeocodeDistributorPoints extends Command
{
    protected $signature = 'distributors:geocode {--id=} {--all}';
    protected $description = 'Geocode distributor points to automatically set lat/lng from ZIP code';

    public function handle(GeocodingService $geocodingService)
    {
        if ($this->option('id')) {
            $distributors = DistributorPoint::where('id', $this->option('id'))->get();
        } elseif ($this->option('all')) {
            $distributors = DistributorPoint::all();
        } else {
            // Geocode only distributors missing lat/lng
            $distributors = DistributorPoint::whereNull('lat')
                ->orWhereNull('lng')
                ->orWhere('lat', 0)
                ->orWhere('lng', 0)
                ->get();
        }

        if ($distributors->isEmpty()) {
            $this->info('No distributors found to geocode.');
            return 0;
        }

        $this->info("Found {$distributors->count()} distributor(s) to geocode.");

        $successCount = 0;
        $failCount = 0;

        foreach ($distributors as $distributor) {
            $this->info("Processing: {$distributor->name} (ZIP: {$distributor->zip_code})");

            try {
                // Use the ZIP code to get coordinates
                $coordinates = $geocodingService->getCoordinates($distributor->zip_code);

                if ($coordinates) {
                    $distributor->lat = $coordinates['lat'];
                    $distributor->lng = $coordinates['lng'];
                    $distributor->save();

                    $this->info("  ✓ Success! Lat: {$coordinates['lat']}, Lng: {$coordinates['lng']}");
                    $successCount++;
                } else {
                    $this->error("  ✗ Failed to geocode ZIP: {$distributor->zip_code}");
                    $failCount++;
                }
            } catch (\Exception $e) {
                $this->error("  ✗ Error: {$e->getMessage()}");
                $failCount++;
            }

            // Small delay to respect API rate limits
            usleep(250000); // 250ms
        }

        $this->newLine();
        $this->info("Complete! Success: {$successCount}, Failed: {$failCount}");

        return 0;
    }
}
