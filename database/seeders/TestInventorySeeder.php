<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add test cart data
        DB::table('carts')->insert([
            'coustomer_id' => 1,
            'product_id' => 1,
            'size_id' => 9,
            'color_id' => 8,
            'quantity' => 2,
            'price' => 100.00,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Add another test cart item
        DB::table('carts')->insert([
            'coustomer_id' => 1,
            'product_id' => 2,
            'size_id' => 3,
            'color_id' => 4,
            'quantity' => 1,
            'price' => 150.00,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->command->info('Test cart data created successfully!');
        
        // Show current cart data
        $carts = DB::table('carts')->get();
        $this->command->info('Current cart count: ' . $carts->count());
        foreach ($carts as $cart) {
            $this->command->info("Cart: Product {$cart->product_id}, Qty: {$cart->quantity}, Customer: {$cart->coustomer_id}");
        }
    }
}