<?php

namespace Database\Seeders;

use App\Models\DistributorPoint;
use Illuminate\Database\Seeder;

class DistributorPointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distributors = [
            [
                'name' => 'New York Distribution Center',
                'address' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'country' => 'USA',
                'phone' => '212-555-0100',
                'email' => 'ny@example.com',
                'status' => 'active',
                // Lat/Lng will be auto-populated by observer
            ],
            [
                'name' => 'Los Angeles Distribution Center',
                'address' => '456 Sunset Blvd',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip_code' => '90001',
                'country' => 'USA',
                'phone' => '213-555-0200',
                'email' => 'la@example.com',
                'status' => 'active',
            ],
            [
                'name' => 'Chicago Distribution Center',
                'address' => '789 Michigan Ave',
                'city' => 'Chicago',
                'state' => 'IL',
                'zip_code' => '60601',
                'country' => 'USA',
                'phone' => '312-555-0300',
                'email' => 'chicago@example.com',
                'status' => 'active',
            ],
            [
                'name' => 'Houston Distribution Center',
                'address' => '321 Texas Ave',
                'city' => 'Houston',
                'state' => 'TX',
                'zip_code' => '77001',
                'country' => 'USA',
                'phone' => '713-555-0400',
                'email' => 'houston@example.com',
                'status' => 'active',
            ],
            [
                'name' => 'Miami Distribution Center',
                'address' => '654 Ocean Dr',
                'city' => 'Miami',
                'state' => 'FL',
                'zip_code' => '33101',
                'country' => 'USA',
                'phone' => '305-555-0500',
                'email' => 'miami@example.com',
                'status' => 'active',
            ],
        ];

        foreach ($distributors as $distributor) {
            DistributorPoint::create($distributor);
        }

        $this->command->info('Created ' . count($distributors) . ' distributor points with auto-geocoding.');
    }
}
