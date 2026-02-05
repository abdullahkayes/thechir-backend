<?php

/*
|--------------------------------------------------------------------------
| Test Checkout Process
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKOUT TEST ===\n\n";

// 1. Check current inventory
echo "1. Current inventory before checkout:\n";
$inventories = DB::table('product_inventories')->get();
foreach ($inventories as $inv) {
    echo "   Product {$inv->product_id}: {$inv->quantity} units (Size: {$inv->size_id}, Color: {$inv->color_id})\n";
}

echo "\n";

// 2. Check cart items
echo "2. Cart items:\n";
$carts = DB::table('carts')->where('coustomer_id', 1)->get();
foreach ($carts as $cart) {
    echo "   Product {$cart->product_id}: {$cart->quantity} units\n";
}

echo "\n";

// 3. Simulate checkout process manually
echo "3. Simulating checkout process...\n";

// Create order
$orderId = 'TEST' . time();
DB::table('orders')->insert([
    'order_id' => $orderId,
    'coustomer_id' => 1,
    'sub_total' => 350.00,
    'total' => 350.00,
    'discount' => 0,
    'payment_method' => 1,
    'coupon' => '',
    'created_at' => now(),
]);

echo "   Created order: {$orderId}\n";

// Create billing
DB::table('billings')->insert([
    'order_id' => $orderId,
    'coustomer_id' => 1,
    'name' => 'Test Customer',
    'company' => '',
    'street' => '123 Test St',
    'apartment' => 'Apt 1',
    'city' => 'Test City',
    'phone' => '1234567890',
    'email' => 'test@example.com',
    'created_at' => now(),
]);

echo "   Created billing record\n";

// Create order products and update inventory
foreach ($carts as $cart) {
    // Create order product
    DB::table('order_products')->insert([
        'order_id' => $orderId,
        'product_id' => $cart->product_id,
        'color_id' => $cart->color_id,
        'size_id' => $cart->size_id,
        'quantity' => $cart->quantity,
        'price' => $cart->price ?? 100.00,
        'created_at' => now(),
    ]);
    
    echo "   Created order product: Product {$cart->product_id} x {$cart->quantity}\n";
    
    // Update inventory
    $query = DB::table('product_inventories')->where('product_id', $cart->product_id);
    
    if ($cart->size_id) {
        $query->where('size_id', $cart->size_id);
    } else {
        $query->whereNull('size_id');
    }
    
    if ($cart->color_id) {
        $query->where('color_id', $cart->color_id);
    } else {
        $query->whereNull('color_id');
    }
    
    $inventory = $query->first();
    
    if ($inventory) {
        $newQuantity = max(0, $inventory->quantity - $cart->quantity);
        DB::table('product_inventories')
            ->where('id', $inventory->id)
            ->update(['quantity' => $newQuantity]);
        
        echo "   Updated inventory: Product {$cart->product_id} {$inventory->quantity} -> {$newQuantity}\n";
    } else {
        echo "   ❌ No inventory record found for Product {$cart->product_id}\n";
    }
}

// Clear cart
DB::table('carts')->where('coustomer_id', 1)->delete();
echo "   Cleared cart\n";

echo "\n";

// 4. Check final inventory
echo "4. Inventory after checkout:\n";
$finalInventories = DB::table('product_inventories')->get();
foreach ($finalInventories as $inv) {
    echo "   Product {$inv->product_id}: {$inv->quantity} units\n";
}

echo "\n";

// 5. Verify order was created
echo "5. Verification:\n";
$order = DB::table('orders')->where('order_id', $orderId)->first();
if ($order) {
    echo "   ✅ Order created successfully\n";
} else {
    echo "   ❌ Order creation failed\n";
}

$orderProducts = DB::table('order_products')->where('order_id', $orderId)->count();
echo "   ✅ Order products created: {$orderProducts} items\n";

$remainingCarts = DB::table('carts')->where('coustomer_id', 1)->count();
echo "   ✅ Cart cleared: {$remainingCarts} items remaining\n";

echo "\n=== CHECKOUT TEST COMPLETE ===\n";