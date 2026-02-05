<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to products table
        Schema::table('products', function (Blueprint $table) {
            $table->index(['category_id', 'subcategory_id']);
            $table->index('brand_id');
            $table->index('sku');
        });

        // Add indexes to stock_details table
        Schema::table('stock_details', function (Blueprint $table) {
            $table->index(['product_id', 'status']);
            $table->index('purchase_order_id');
            $table->index(['expiry_date', 'remaining_quantity']);
            $table->index('received_date');
        });

        // Add indexes to inventory_movements table
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->index(['product_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('movement_type');
        });

        // Add indexes to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->index('coustomer_id');
            $table->index('created_at');
        });

        // Add indexes to order_products table
        Schema::table('order_products', function (Blueprint $table) {
            $table->index(['order_id', 'product_id']);
        });

        // Add indexes to accounting_entries table
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->index(['reference_type', 'reference_id']);
            $table->index('entry_date');
            $table->index('status');
        });

        // Add indexes to purchase_orders table
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index('supplier_id');
            $table->index('status');
            $table->index('expected_delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'subcategory_id']);
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['sku']);
        });

        // Drop indexes from stock_details table
        Schema::table('stock_details', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'status']);
            $table->dropIndex(['purchase_order_id']);
            $table->dropIndex(['expiry_date', 'remaining_quantity']);
            $table->dropIndex(['received_date']);
        });

        // Drop indexes from inventory_movements table
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'created_at']);
            $table->dropIndex(['reference_type', 'reference_id']);
            $table->dropIndex(['movement_type']);
        });

        // Drop indexes from orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['coustomer_id']);
            $table->dropIndex(['created_at']);
        });

        // Drop indexes from order_products table
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'product_id']);
        });

        // Drop indexes from accounting_entries table
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->dropIndex(['reference_type', 'reference_id']);
            $table->dropIndex(['entry_date']);
            $table->dropIndex(['status']);
        });

        // Drop indexes from purchase_orders table
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['expected_delivery_date']);
        });
    }
};