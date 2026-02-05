<?php

namespace App\Http\Controllers;

use App\Models\DistributorPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class DistributorPointController extends Controller
{
    public function index()
    {
        $distributorPoints = DistributorPoint::all();

        return view('Backend.distributor-points', compact('distributorPoints'));
    }

    public function edit(DistributorPoint $distributorPoint)
    {
        return view('Backend.distributor-points-edit', compact('distributorPoint'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_maps_api_key' => 'required|string',
            'locationiq_api_key' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Auto-generate name and address
        $name = $request->city . ' Distribution Center';
        $address = $request->city . ', ' . $request->state . ' ' . $request->zip_code;

        // Try to geocode the address (with error handling)
        $coordinates = ['lat' => 0, 'lng' => 0];
        try {
            $coordinates = $this->geocodeAddress($request->city, $request->state, $request->zip_code);
        } catch (\Exception $e) {
            // Continue with default coordinates if geocoding fails
        }

        try {
            $distributorPoint = DistributorPoint::create([
                'name' => $name,
                'address' => $address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'lat' => $coordinates['lat'] ?? 0,
                'lng' => $coordinates['lng'] ?? 0,
                'status' => 'active',
                'google_maps_api_key' => $request->google_maps_api_key,
                'locationiq_api_key' => $request->locationiq_api_key,
            ]);

            // Use the distributor point's ID as the index for API keys
            $apiKeyIndex = $distributorPoint->id;

            // Add API keys to .env file
            $this->addApiKeyToEnv('GOOGLE_MAPS_API_KEY_' . $apiKeyIndex, $request->google_maps_api_key);
            $this->addApiKeyToEnv('LOCATIONIQ_API_KEY_' . $apiKeyIndex, $request->locationiq_api_key);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to save distributor point: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('admin.distributor-points.index')->with('success', 'Distributor point created successfully');
    }

    public function update(Request $request, DistributorPoint $distributorPoint)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
            'google_maps_api_key' => 'nullable|string',
            'locationiq_api_key' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        // Use the distributor point's ID as the index for API keys
        $apiKeyIndex = $distributorPoint->id;

        // Update distributor point data
        $updateData = [
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'lat' => $request->latitude,
            'lng' => $request->longitude,
            'status' => $request->has('is_active') ? ($request->is_active ? 'active' : 'inactive') : 'inactive',
        ];

        // Update API keys only if provided
        if ($request->filled('google_maps_api_key')) {
            $updateData['google_maps_api_key'] = $request->google_maps_api_key;
            // Update .env file
            $this->addApiKeyToEnv('GOOGLE_MAPS_API_KEY_' . $apiKeyIndex, $request->google_maps_api_key);
        }

        if ($request->filled('locationiq_api_key')) {
            $updateData['locationiq_api_key'] = $request->locationiq_api_key;
            // Update .env file
            $this->addApiKeyToEnv('LOCATIONIQ_API_KEY_' . $apiKeyIndex, $request->locationiq_api_key);
        }

        $distributorPoint->update($updateData);

        if ($request->ajax()) {
            return response()->json(['message' => 'Distributor point updated successfully']);
        } else {
            return redirect()->route('admin.distributor-points.index')->with('success', 'Distributor point updated successfully');
        }
    }

    public function updateApiKeys(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_maps_api_key' => 'required|string',
            'locationiq_api_key' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Get next index for API keys
        $index = $this->getNextApiKeyIndex();

        // Auto-generate name and address
        $name = $request->city . ' Distribution Center';
        $address = $request->city . ', ' . $request->state . ' ' . $request->zip_code;

        // Try to geocode the address (with error handling)
        $coordinates = ['lat' => 0, 'lng' => 0];
        try {
            $coordinates = $this->geocodeAddress($request->city, $request->state, $request->zip_code);
        } catch (\Exception $e) {
            // Continue with default coordinates if geocoding fails
        }

        try {
            $distributorPoint = DistributorPoint::create([
                'name' => $name,
                'address' => $address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'lat' => $coordinates['lat'] ?? 0,
                'lng' => $coordinates['lng'] ?? 0,
                'status' => 'active',
                'google_maps_api_key' => $request->google_maps_api_key,
                'locationiq_api_key' => $request->locationiq_api_key,
            ]);

            // Use the distributor point's ID as the index for API keys
            $apiKeyIndex = $distributorPoint->id;

            // Add API keys to .env file
            $this->addApiKeyToEnv('GOOGLE_MAPS_API_KEY_' . $apiKeyIndex, $request->google_maps_api_key);
            $this->addApiKeyToEnv('LOCATIONIQ_API_KEY_' . $apiKeyIndex, $request->locationiq_api_key);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to save distributor point: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('admin.distributor-points.index')->with('success', 'Distributor point created successfully');
    }

    public function destroy(DistributorPoint $distributorPoint)
    {
        // Use the distributor point's ID as the index for API keys
        $apiKeyIndex = $distributorPoint->id;

        // Delete the distributor point
        $distributorPoint->delete();

        // Remove API keys from .env file
        $this->removeApiKeyFromEnv('GOOGLE_MAPS_API_KEY_' . $apiKeyIndex);
        $this->removeApiKeyFromEnv('LOCATIONIQ_API_KEY_' . $apiKeyIndex);

        if (request()->ajax()) {
            return response()->json(['message' => 'Distributor point deleted successfully']);
        }

        return redirect()->route('admin.distributor-points.index')->with('success', 'Distributor point and API keys deleted successfully');
    }

    // API endpoint for frontend to get active distributor points
    public function getActivePoints()
    {
        $points = DistributorPoint::active()->get(['id', 'name', 'latitude', 'longitude', 'city', 'state']);

        return response()->json($points);
    }

    // API endpoint for frontend to get API keys
    public function getApiKeys()
    {
        return response()->json([
            'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
            'locationiq_api_key' => env('LOCATIONIQ_API_KEY', '')
        ]);
    }

    // Get API key for a specific distributor point
    public function getApiKeyForPoint($pointId)
    {
        $point = DistributorPoint::find($pointId);

        if (!$point) {
            return response()->json([
                'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
                'locationiq_api_key' => env('LOCATIONIQ_API_KEY', '')
            ]);
        }

        return response()->json([
            'google_maps_api_key' => $point->google_maps_api_key ?? env('GOOGLE_MAPS_API_KEY', ''),
            'locationiq_api_key' => $point->locationiq_api_key ?? env('LOCATIONIQ_API_KEY', '')
        ]);
    }

    // Geocode address using available APIs
    private function geocodeAddress($city, $state, $zipCode)
    {
        $address = "{$city}, {$state} {$zipCode}, USA";

        // Try LocationIQ first
        $locationIqKey = env('LOCATIONIQ_API_KEY');
        if ($locationIqKey && $locationIqKey !== 'pk.17c79dc3f274522f024e4c95cdfd5c12') {
            try {
                $url = "https://us1.locationiq.com/v1/search.php?key={$locationIqKey}&q=" . urlencode($address) . "&format=json&limit=1";
                $response = file_get_contents($url);
                $data = json_decode($response, true);

                if ($data && count($data) > 0 && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                    return ['lat' => floatval($data[0]['lat']), 'lng' => floatval($data[0]['lon'])];
                }
            } catch (\Exception $e) {
                // Continue to next API
            }
        }

        // Try Google Maps API
        $googleKey = env('GOOGLE_MAPS_API_KEY');
        if ($googleKey && $googleKey !== 'AIzaSyC5KbnBjjXTqjdLOhSh1b1fv79XKisCJ_0') {
            try {
                $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key={$googleKey}";
                $response = file_get_contents($url);
                $data = json_decode($response, true);

                if ($data['status'] === 'OK' && count($data['results']) > 0) {
                    $location = $data['results'][0]['geometry']['location'];
                    return ['lat' => floatval($location['lat']), 'lng' => floatval($location['lng'])];
                }
            } catch (\Exception $e) {
                // Continue to fallback
            }
        }

        // Try OpenStreetMap Nominatim as fallback
        try {
            $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address) . "&limit=1&countrycodes=us";
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            if ($data && count($data) > 0 && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                return ['lat' => floatval($data[0]['lat']), 'lng' => floatval($data[0]['lon'])];
            }
        } catch (\Exception $e) {
            // All geocoding failed
        }

        // Return null if all geocoding fails - coordinates will be set to 0
        // Admin can manually update coordinates if needed
        return ['lat' => 0, 'lng' => 0];
    }

    /**
     * Get the next available index for API keys in .env file
     */
    private function getNextApiKeyIndex()
    {
        $envFile = base_path('.env');
        $envContent = File::get($envFile);

        $maxIndex = 0;

        // Find all existing indexed API keys
        preg_match_all('/GOOGLE_MAPS_API_KEY_(\d+)/', $envContent, $matches);
        if (!empty($matches[1])) {
            $maxIndex = max(array_map('intval', $matches[1]));
        }

        return $maxIndex + 1;
    }

    /**
     * Add or update an API key in the .env file
     */
    private function addApiKeyToEnv($key, $value)
    {
        $envFile = base_path('.env');
        $envContent = File::get($envFile);

        // Check if the key already exists (with or without index)
        if (preg_match('/^' . $key . '=.*/m', $envContent)) {
            // Update existing key
            $envContent = preg_replace('/^' . $key . '=.*/m', $key . '=' . $value, $envContent);
        } else {
            // Add new key
            $envContent .= "\n" . $key . "=" . $value;
        }

        File::put($envFile, $envContent);
    }

    /**
     * Remove an API key from the .env file
     */
    private function removeApiKeyFromEnv($key)
    {
        $envFile = base_path('.env');
        $envContent = File::get($envFile);

        // Remove the key from .env file
        $envContent = preg_replace('/^' . $key . '=.*\n?/m', '', $envContent);

        File::put($envFile, $envContent);
    }

    /**
     * Get the index for a specific distributor point
     */
    private function getDistributorPointIndex($pointId)
    {
        $point = DistributorPoint::find($pointId);
        if (!$point) {
            return null;
        }

        $envFile = base_path('.env');
        $envContent = File::get($envFile);

        // Search for the API keys associated with this distributor point
        // We'll use the order of creation to determine the index
        $allPoints = DistributorPoint::orderBy('id')->pluck('id')->toArray();
        $position = array_search($pointId, $allPoints);

        return $position !== false ? $position + 1 : null;
    }
}
