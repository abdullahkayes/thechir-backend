<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CheckoutController;
use App\Models\ProductInventory;
use App\Models\Cart;

/*
|--------------------------------------------------------------------------
| Test Route for Inventory Deduction
|--------------------------------------------------------------------------
*/

Route::get('/test-inventory', function () {
    // Get first product inventory record
    $inventory = ProductInventory::first();
    
    if (!$inventory) {
        return response()->json(['error' => 'No inventory records found']);
    }
    
    return response()->json([
        'message' => 'Current Inventory',
        'product_id' => $inventory->product_id,
        'current_quantity' => $inventory->quantity,
        'size_id' => $inventory->size_id,
        'color_id' => $inventory->color_id
    ]);
});

Route::post('/test-inventory-deduct', function () {
    // Get first product inventory record
    $inventory = ProductInventory::first();
    
    if (!$inventory) {
        return response()->json(['error' => 'No inventory records found']);
    }
    
    $originalQuantity = $inventory->quantity;
    $deductQuantity = 1;
    
    // Update inventory
    $newQuantity = max(0, $originalQuantity - $deductQuantity);
    $inventory->update(['quantity' => $newQuantity]);
    
    return response()->json([
        'message' => 'Inventory Updated Successfully',
        'product_id' => $inventory->product_id,
        'original_quantity' => $originalQuantity,
        'deducted_quantity' => $deductQuantity,
        'new_quantity' => $newQuantity
    ]);
});

Route::get('/test-carts', function () {
    $carts = Cart::with('rel_to_product')->limit(5)->get();
    
    return response()->json([
        'message' => 'Cart Items',
        'count' => $carts->count(),
        'items' => $carts->map(function($cart) {
            return [
                'id' => $cart->id,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'size_id' => $cart->size_id,
                'color_id' => $cart->color_id
            ];
        })
    ]);
});