<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

try {
    $count = Product::count();
    echo "PRODUCT_COUNT=" . $count . "\n";
    $p = Product::with(['inventories','rel_to_gal'])->first();
    if ($p) {
        echo "SAMPLE_PRODUCT_ID=" . $p->id . "\n";
        echo "HAS_INVENTORIES=" . ($p->inventories->count()) . "\n";
        echo "GALLERIES=" . ($p->rel_to_gal->count()) . "\n";
    } else {
        echo "SAMPLE_PRODUCT=null\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
