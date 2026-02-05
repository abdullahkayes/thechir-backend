<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Product;

echo "Testing product data retrieval...\n";

// Count products
$count = Product::count();
echo "Total products in database: $count\n";

if ($count > 0) {
    // Get one product with relationships
    $product = Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->first();
    echo "\nFirst product: \n";
    echo "ID: " . ($product->id ?? 'null') . "\n";
    echo "Name: " . ($product->name ?? 'null') . "\n";
    echo "Has galleries: " . ($product->rel_to_gal ? $product->rel_to_gal->count() : 0) . "\n";
    echo "Has inventories: " . ($product->inventories ? $product->inventories->count() : 0) . "\n";
} else {
    echo "No products found in database!\n";
}

// Check if there are any inventory records
$inventoryCount = \App\Models\ProductInventory::count();
echo "\nTotal inventory records: $inventoryCount\n";

// Check table schema
echo "\n\nProduct table columns:\n";
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('products');
print_r($columns);
