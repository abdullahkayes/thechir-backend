<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Cart;
use App\Models\ProductInventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "Updating cart weights...\n";

// Get all cart items that don't have weight_grams set or have default 500
$carts = Cart::where('weight_grams', '<=', 500)->get();

echo "Found " . count($carts) . " cart items to update\n";

foreach ($carts as $cart) {
    // Get inventory for this cart item
    $inventory = ProductInventory::where('product_id', $cart->product_id)
        ->when($cart->color_id, fn($q) => $q->where('color_id', $cart->color_id))
        ->when($cart->size_id, fn($q) => $q->where('size_id', $cart->size_id))
        ->first();

    if ($inventory) {
        // Calculate correct weight: product weight * quantity
        $correctWeight = $inventory->weight_grams * $cart->quantity;

        echo "Updating cart ID {$cart->id}: product_id={$cart->product_id}, quantity={$cart->quantity}, old_weight={$cart->weight_grams}, new_weight={$correctWeight}\n";

        // Update the cart item
        $cart->weight_grams = $correctWeight;
        $cart->save();
    } else {
        echo "No inventory found for cart ID {$cart->id}, product_id={$cart->product_id}\n";
    }
}

echo "\nDone!\n";
