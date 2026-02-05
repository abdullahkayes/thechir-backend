<?php

/*
|--------------------------------------------------------------------------
| Test Stock Adjustment Functionality
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING STOCK ADJUSTMENT ===\n\n";

// 1. Check current inventory
echo "1. Current inventory before adjustment:\n";
$inventories = DB::table('product_inventories')->limit(3)->get();
foreach ($inventories as $inv) {
    echo "   Product {$inv->product_id}: {$inv->quantity} units\n";
}

echo "\n";

// 2. Test adding stock
echo "2. Testing ADD stock adjustment...\n";
$firstProduct = $inventories->first();
if ($firstProduct) {
    $originalQty = $firstProduct->quantity;
    $addQty = 5;

    echo "   Adding {$addQty} units to Product {$firstProduct->product_id}\n";

    // Simulate the form submission
    $_POST = [
        'type' => 'ADJUSTMENT',
        'quantity' => $addQty,
        'reason' => 'Test stock addition',
        'notes' => 'Automated test'
    ];

    // Call the controller method directly
    $controller = new \App\Http\Controllers\ProductInventoryController();
    $request = new \Illuminate\Http\Request($_POST);

    try {
        $response = $controller->adjustStock($request, $firstProduct->product_id);
        echo "   ✅ Add stock request submitted\n";

        // Check if quantity was updated
        $updatedInventory = DB::table('product_inventories')->where('id', $firstProduct->id)->first();
        echo "   New quantity: {$updatedInventory->quantity} (expected: " . ($originalQty + $addQty) . ")\n";

        if ($updatedInventory->quantity == $originalQty + $addQty) {
            echo "   ✅ Add stock test PASSED\n";
        } else {
            echo "   ❌ Add stock test FAILED\n";
        }

    } catch (Exception $e) {
        echo "   ❌ Add stock test FAILED: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ No inventory found to test\n";
}

echo "\n";

// 3. Test subtracting stock
echo "3. Testing SUBTRACT stock adjustment...\n";
$secondProduct = $inventories->skip(1)->first();
if ($secondProduct && $secondProduct->quantity > 0) {
    $originalQty = $secondProduct->quantity;
    $subtractQty = min(2, $secondProduct->quantity); // Don't subtract more than available

    echo "   Subtracting {$subtractQty} units from Product {$secondProduct->product_id}\n";

    // Simulate the form submission
    $_POST = [
        'type' => 'DAMAGE',
        'quantity' => $subtractQty,
        'reason' => 'Test stock subtraction',
        'notes' => 'Automated test'
    ];

    // Call the controller method directly
    $controller = new \App\Http\Controllers\ProductInventoryController();
    $request = new \Illuminate\Http\Request($_POST);

    try {
        $response = $controller->adjustStock($request, $secondProduct->product_id);
        echo "   ✅ Subtract stock request submitted\n";

        // Check if quantity was updated
        $updatedInventory = DB::table('product_inventories')->where('id', $secondProduct->id)->first();
        echo "   New quantity: {$updatedInventory->quantity} (expected: " . ($originalQty - $subtractQty) . ")\n";

        if ($updatedInventory->quantity == $originalQty - $subtractQty) {
            echo "   ✅ Subtract stock test PASSED\n";
        } else {
            echo "   ❌ Subtract stock test FAILED\n";
        }

    } catch (Exception $e) {
        echo "   ❌ Subtract stock test FAILED: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ No inventory with stock found to test subtraction\n";
}

echo "\n";

// 4. Check inventory movements were logged
echo "4. Checking inventory movements...\n";
$recentMovements = DB::table('inventory_movements')
    ->where('reference_type', 'stock_adjustment')
    ->latest()
    ->limit(5)
    ->get();

echo "   Found " . $recentMovements->count() . " recent adjustment movements\n";
foreach ($recentMovements as $movement) {
    echo "   Product {$movement->product_id}: {$movement->movement_type} {$movement->quantity} units - {$movement->reason}\n";
}

echo "\n=== STOCK ADJUSTMENT TEST COMPLETE ===\n";