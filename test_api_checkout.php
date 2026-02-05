<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

// Set up Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== API Checkout Test ===\n";

try {
    // Prepare test data that matches what the Vue component sends
    $testData = [
        'coustomer_id' => 1,
        'sub_total' => 1000,
        'total' => 1000,
        'discount' => 0,
        'payment_method' => '1',
        'coupon' => '',
        'name' => 'John Doe',
        'company' => 'Test Company',
        'street' => '123 Test Street',
        'apartment' => 'Apt 1',
        'city' => 'Test City',
        'phone' => '1234567890',
        'email' => 'john@example.com',
    ];
    
    echo "Test data prepared:\n";
    print_r($testData);
    
    echo "\n2. Making API request to checkout endpoint...\n";
    
    // Make the API request
    $response = Http::post('http://127.0.0.1:8000/api/checkout', $testData);
    
    echo "Response status: " . $response->status() . "\n";
    echo "Response body: " . $response->body() . "\n";
    
    if ($response->successful()) {
        echo "SUCCESS: API call completed successfully\n";
        $responseData = $response->json();
        print_r($responseData);
    } else {
        echo "ERROR: API call failed\n";
        echo "Error details: " . $response->error() . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== End API Test ===\n";