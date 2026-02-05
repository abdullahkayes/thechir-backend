<?php

/*
|--------------------------------------------------------------------------
| Test Expiry Date System
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING EXPIRY DATE SYSTEM ===\n\n";

// 1. Add test products with expiry dates
echo "1. Adding test products with expiry dates...\n";

$testProducts = [
    [
        'product_id' => 1,
        'size_id' => 9,
        'color_id' => 8,
        'quantity' => 10,
        'expiry_date' => now()->addDays(5)->format('Y-m-d'), // Expires in 5 days
        'manufacture_date' => now()->subDays(10)->format('Y-m-d'),
        'batch_number' => 'TEST-EXPIRED-001'
    ],
    [
        'product_id' => 2,
        'size_id' => 3,
        'color_id' => 4,
        'quantity' => 15,
        'expiry_date' => now()->subDays(2)->format('Y-m-d'), // Already expired
        'manufacture_date' => now()->subDays(20)->format('Y-m-d'),
        'batch_number' => 'TEST-EXPIRING-002'
    ],
    [
        'product_id' => 3,
        'size_id' => 3,
        'color_id' => 4,
        'quantity' => 20,
        'expiry_date' => now()->addDays(60)->format('Y-m-d'), // Valid for 60 days
        'manufacture_date' => now()->subDays(5)->format('Y-m-d'),
        'batch_number' => 'TEST-VALID-003'
    ]
];

foreach ($testProducts as $product) {
    // Check if this combination already exists
    $existing = DB::table('product_inventories')
        ->where('product_id', $product['product_id'])
        ->where('size_id', $product['size_id'])
        ->where('color_id', $product['color_id'])
        ->first();

    if ($existing) {
        // Update existing record
        DB::table('product_inventories')
            ->where('id', $existing->id)
            ->update($product);
        echo "   Updated existing inventory for Product {$product['product_id']}\n";
    } else {
        // Insert new record
        DB::table('product_inventories')->insert(array_merge($product, [
            'buy_price' => 50.00,
            'price' => 100.00,
            'discount_price' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]));
        echo "   Added new inventory for Product {$product['product_id']}\n";
    }
}

echo "\n";

// 2. Test expiry status checking
echo "2. Testing expiry status checking...\n";

$inventories = DB::table('product_inventories')->get();

foreach ($inventories as $inventory) {
    $productInventory = \App\Models\ProductInventory::find($inventory->id);

    $status = $productInventory->getExpiryStatus();
    $daysUntilExpiry = $productInventory->getDaysUntilExpiry();

    echo "   Product {$inventory->product_id} (Batch: {$inventory->batch_number}):\n";
    echo "     - Status: {$status}\n";
    echo "     - Days until expiry: " . ($daysUntilExpiry !== null ? $daysUntilExpiry : 'N/A') . "\n";
    echo "     - Is expired: " . ($productInventory->isExpired() ? 'YES' : 'NO') . "\n";
    echo "     - Is expiring soon: " . ($productInventory->isExpiringSoon() ? 'YES' : 'NO') . "\n";
    echo "\n";
}

// 3. Test expiry alerts API
echo "3. Testing expiry alerts API...\n";

$controller = new \App\Http\Controllers\ProductInventoryController();
$alertsResponse = $controller->getExpiryAlerts();
$alerts = json_decode($alertsResponse->getContent(), true);

echo "   Found " . count($alerts) . " expiry alerts:\n";
foreach ($alerts as $alert) {
    echo "   - {$alert['type']}: {$alert['product_name']} - {$alert['quantity']} units\n";
    if ($alert['type'] === 'expiring_soon') {
        echo "     Days until expiry: {$alert['days_until_expiry']}\n";
    } else {
        echo "     Days expired: {$alert['days_expired']}\n";
    }
}

echo "\n";

// 4. Test dashboard expiry counts
echo "4. Testing dashboard expiry counts...\n";

$expiredCount = DB::table('product_inventories')
    ->whereNotNull('expiry_date')
    ->where('expiry_date', '<=', now())
    ->where('quantity', '>', 0)
    ->count();

$expiringSoonCount = DB::table('product_inventories')
    ->whereNotNull('expiry_date')
    ->where('expiry_date', '>', now())
    ->where('expiry_date', '<=', now()->addDays(30))
    ->where('quantity', '>', 0)
    ->count();

echo "   Expired products: {$expiredCount}\n";
echo "   Products expiring soon: {$expiringSoonCount}\n";

echo "\n=== EXPIRY SYSTEM TEST COMPLETE ===\n";
echo "\nNext steps:\n";
echo "1. Visit /inventory/index to see expiry warnings in the UI\n";
echo "2. Check the red signals for expired products\n";
echo "3. Check the yellow warnings for products expiring soon\n";
echo "4. Use the stock adjustment feature to manage expired inventory\n";