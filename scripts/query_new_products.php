<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

try {
    $products = Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->latest()->take(4)->get();
    echo json_encode(['count' => $products->count(), 'items' => $products->map(function($p){ return $p->only(['id','product_name','category_id']); })->values()], JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
