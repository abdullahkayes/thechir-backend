<?php

/*
|--------------------------------------------------------------------------
| Debug Inventory Deduction Script
|--------------------------------------------------------------------------
|
| This script helps debug inventory deduction issues by:
| 1. Checking if ProductInventory table has data
| 2. Testing direct inventory updates
| 3. Checking cart data
|
*/

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== INVENTORY DEBUG SCRIPT ===\n\n";

// 1. Check ProductInventory table
echo "1. Checking ProductInventory table...\n";
$inventories = DB::table('product_inventories')->limit(5)->get();
echo "Found " . $inventories->count() . " inventory records\n";

if ($inventories->count() > 0) {
    foreach ($inventories as $inv) {
        echo "   Product ID: {$inv->product_id}, Quantity: {$inv->quantity}, Size: {$inv->size_id}, Color: {$inv->color_id}\n";
    }
} else {
    echo "   ❌ No inventory records found! You need to add some inventory data.\n";
}

echo "\n";

// 2. Check Cart table
echo "2. Checking Cart table...\n";
$carts = DB::table('carts')->limit(5)->get();
echo "Found " . $carts->count() . " cart records\n";

if ($carts->count() > 0) {
    foreach ($carts as $cart) {
        echo "   Product ID: {$cart->product_id}, Quantity: {$cart->quantity}, Customer: {$cart->coustomer_id}\n";
    }
} else {
    echo "   ❌ No cart records found! You need to add some cart data.\n";
}

echo "\n";

// 3. Test direct inventory update
echo "3. Testing direct inventory update...\n";
if ($inventories->count() > 0) {
    $firstInventory = $inventories->first();
    $originalQty = $firstInventory->quantity;
    
    echo "   Updating product {$firstInventory->product_id} quantity from {$originalQty} to " . ($originalQty - 1) . "\n";
    
    $updated = DB::table('product_inventories')
        ->where('id', $firstInventory->id)
        ->update(['quantity' => $originalQty - 1]);
    
    if ($updated) {
        echo "   ✅ Inventory update successful!\n";
        
        // Verify the update
        $newInventory = DB::table('product_inventories')->where('id', $firstInventory->id)->first();
        echo "   New quantity: {$newInventory->quantity}\n";
        
        // Revert the change
        DB::table('product_inventories')
            ->where('id', $firstInventory->id)
            ->update(['quantity' => $originalQty]);
        echo "   🔄 Reverted to original quantity: {$originalQty}\n";
    } else {
        echo "   ❌ Inventory update failed!\n";
    }
} else {
    echo "   ❌ Cannot test - no inventory records found\n";
}

echo "\n";

// 4. Check for any foreign key constraints
echo "4. Checking foreign key constraints...\n";
try {
    $products = DB::table('products')->count();
    $sizes = DB::table('sizes')->count();
    $colors = DB::table('colors')->count();
    
    echo "   Products: {$products}, Sizes: {$sizes}, Colors: {$colors}\n";
    
    if ($products == 0) {
        echo "   ⚠️  No products found - inventory needs products to reference\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error checking foreign key tables: " . $e->getMessage() . "\n";
}

echo "\n=== RECOMMENDATIONS ===\n";

if ($inventories->count() == 0) {
    echo "1. Add some inventory data to the product_inventories table\n";
    echo "   Example SQL:\n";
    echo "   INSERT INTO product_inventories (product_id, size_id, color_id, price, quantity) VALUES (1, NULL, NULL, 100.00, 10);\n";
}

if ($carts->count() == 0) {
    echo "2. Add some cart data to test the checkout process\n";
    echo "   Example SQL:\n";
    echo "   INSERT INTO carts (coustomer_id, product_id, size_id, color_id, quantity) VALUES (1, 1, NULL, NULL, 2);\n";
}

echo "3. Check Laravel logs for any errors: storage/logs/laravel.log\n";
echo "4. Run the test routes:\n";
echo "   GET /test-inventory\n";
echo "   POST /test-inventory-deduct\n";
echo "   GET /test-carts\n";

echo "\n=== DEBUG COMPLETE ===\n";