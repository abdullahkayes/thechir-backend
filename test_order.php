<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Carbon\Carbon;

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Create test color and size
$color = \App\Models\Color::first();
if (!$color) {
    $color = \App\Models\Color::create([
        'color_name' => 'Red',
        'color_code' => '#FF0000'
    ]);
}

$size = \App\Models\Size::first();
if (!$size) {
    $size = \App\Models\Size::create([
        'size' => 'M'
    ]);
}

// Create a test product
$product = Product::first();
if (!$product) {
    $product = Product::create([
        'product_name' => 'Test Product',
        'price' => 29.99,
        'discount_price' => 24.99,
        'category_id' => 1,
        'subcategory_id' => 1,
        'brand_id' => 1,
        'weight' => 0.5,
        'preview' => 'test.jpg'
    ]);
}

// Create a test order
$orderId = 'TEST-' . uniqid();
$order = Order::create([
    'order_id' => $orderId,
    'coustomer_id' => 1,
    'sub_total' => 49.98,
    'total' => 54.98,
    'discount' => 0,
    'delivery_charge' => 5.00,
    'payment_method' => 2,
    'coupon' => '',
    'status' => 1,
    'created_at' => Carbon::now()
]);

// Create test order products
OrderProduct::create([
    'order_id' => $orderId,
    'product_id' => $product->id,
    'quantity' => 2,
    'price' => 24.99,
    'sell_price' => 24.99,
    'cost_price' => 15.00,
    'color_id' => $color->id,
    'size_id' => $size->id
]);

echo 'Test order created: ' . $orderId;
