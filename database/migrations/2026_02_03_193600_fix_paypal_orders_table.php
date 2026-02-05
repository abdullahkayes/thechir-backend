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
        Schema::table('paypal_orders', function (Blueprint $table) {
            // Add missing amazon_id column
            if (!Schema::hasColumn('paypal_orders', 'amazon_id')) {
                $table->unsignedBigInteger('amazon_id')->nullable()->after('distributer_id');
                $table->foreign('amazon_id')->references('id')->on('amazons')->onDelete('set null');
            }
            
            // Ensure sub_total column exists and is decimal type
            if (!Schema::hasColumn('paypal_orders', 'sub_total')) {
                $table->decimal('sub_total', 10, 2)->after('order_id');
            } else {
                // Check if column type is decimal
                $columnType = DB::getSchemaBuilder()->getColumnType('paypal_orders', 'sub_total');
                if ($columnType !== 'decimal') {
                    $table->decimal('sub_total', 10, 2)->change();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paypal_orders', function (Blueprint $table) {
            if (Schema::hasColumn('paypal_orders', 'amazon_id')) {
                $table->dropForeign(['amazon_id']);
                $table->dropColumn('amazon_id');
            }
        });
    }
};
