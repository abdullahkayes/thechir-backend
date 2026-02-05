<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Capsule\Manager as DB;

// Set up Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Stripe Orders Table Insert Test ===\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $orders = App\Models\Order::count();
    echo "Orders table accessible. Count: " . $orders . "\n";
    
    // Check table structure
    echo "2. Checking table structure...\n";
    $columns = Schema::getColumnListing('stripe_orders');
    echo "Table columns: " . implode(', ', $columns) . "\n";
    
    // Test exact data structure that controller will use
    echo "3. Testing Order model with exact controller data structure...\n";
    $testOrderData = [
        'order_id' => 'test_stripe_order_' . time(),
        'coustomer_id' => (int)1,
        'sub_total' => (int)1000,
        'total' => (int)1000,
        'discount' => (int)0,
        'payment_method' => (int)1,
        'coupon' => null, // Empty string converted to null for nullable field
        'name' => 'Test Customer',
        'company' => null, // Empty string converted to null for nullable field
        'street' => 'Test Street',
        'apartment' => 'Test Apartment',
        'city' => 'Test City',
        'phone' => '1234567890',
        'email' => 'test@example.com',
        // Note: NOT including created_at - timestamps() handles this automatically
    ];
    
    echo "Test data prepared (matching controller exactly):\n";
    print_r($testOrderData);
    
    echo "4. Attempting to insert test order...\n";
    $result = App\Models\Order::insert($testOrderData);
    echo "Insert result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    // Check if order was created
    $createdOrder = App\Models\Order::where('order_id', $testOrderData['order_id'])->first();
    if ($createdOrder) {
        echo "5. Order created successfully!\n";
        echo "Order ID: " . $createdOrder->order_id . "\n";
        echo "Name: " . $createdOrder->name . "\n";
        echo "Email: " . $createdOrder->email . "\n";
        echo "Created At: " . $createdOrder->created_at . "\n";
        
        // Clean up test data
        echo "6. Cleaning up test data...\n";
        App\Models\Order::where('order_id', $testOrderData['order_id'])->delete();
        echo "Test data cleaned up.\n";
    } else {
        echo "5. Failed to create order - no order found with ID: " . $testOrderData['order_id'] . "\n";
    }
    
    // Test with problematic data (empty strings for nullable fields)
    echo "7. Testing with empty strings for nullable fields...\n";
    $testOrderData2 = [
        'order_id' => 'test_stripe_order_' . (time() + 1),
        'coustomer_id' => (int)1,
        'sub_total' => (int)500,
        'total' => (int)500,
        'discount' => (int)0,
        'payment_method' => (int)1,
        'coupon' => '', // Empty string
        'name' => 'Test Customer 2',
        'company' => '', // Empty string
        'street' => 'Test Street 2',
        'apartment' => 'Test Apartment 2',
        'city' => 'Test City 2',
        'phone' => '0987654321',
        'email' => 'test2@example.com',
    ];
    
    echo "Testing with empty strings...\n";
    $result2 = App\Models\Order::insert($testOrderData2);
    echo "Insert result with empty strings: " . ($result2 ? 'SUCCESS' : 'FAILED') . "\n";
    
    if ($result2) {
        $createdOrder2 = App\Models\Order::where('order_id', $testOrderData2['order_id'])->first();
        if ($createdOrder2) {
            echo "Order with empty strings created successfully!\n";
            // Clean up
            App\Models\Order::where('order_id', $testOrderData2['order_id'])->delete();
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== End Test ===\n";