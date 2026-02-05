<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Capsule\Manager as DB;

// Set up Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checkout Debug Script ===\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $orders = App\Models\Order::count();
    echo "Orders table accessible. Count: " . $orders . "\n";
    
    // Test if we can create an order
    echo "2. Testing Order model creation...\n";
    $testOrderData = [
        'order_id' => 'test_order_' . time(),
        'coustomer_id' => 1,
        'sub_total' => 1000,
        'total' => 1000,
        'discount' => 0,
        'payment_method' => 1,
        'coupon' => 'TEST',
        'name' => 'Test Customer',
        'company' => 'Test Company',
        'street' => 'Test Street',
        'apartment' => 'Test Apartment',
        'city' => 'Test City',
        'phone' => '1234567890',
        'email' => 'test@example.com',
        'created_at' => new DateTime(),
    ];
    
    echo "Test data prepared:\n";
    print_r($testOrderData);
    
    echo "3. Attempting to insert test order...\n";
    $result = App\Models\Order::insert($testOrderData);
    echo "Insert result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    // Check if order was created
    $createdOrder = App\Models\Order::where('order_id', $testOrderData['order_id'])->first();
    if ($createdOrder) {
        echo "4. Order created successfully!\n";
        echo "Order ID: " . $createdOrder->order_id . "\n";
        echo "Name: " . $createdOrder->name . "\n";
        
        // Clean up test data
        echo "5. Cleaning up test data...\n";
        App\Models\Order::where('order_id', $testOrderData['order_id'])->delete();
        echo "Test data cleaned up.\n";
    } else {
        echo "4. Failed to create order - no order found with ID: " . $testOrderData['order_id'] . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== End Debug Script ===\n";