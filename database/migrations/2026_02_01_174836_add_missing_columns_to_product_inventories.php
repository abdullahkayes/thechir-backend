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
        Schema::table('product_inventories', function (Blueprint $table) {
            $table->decimal('discount_price', 15, 2)->nullable()->after('price');
            $table->date('manufacture_date')->nullable()->after('expiry_date');
            $table->string('batch_number')->nullable()->after('manufacture_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_inventories', function (Blueprint $table) {
            $table->dropColumn(['discount_price', 'manufacture_date', 'batch_number']);
        });
    }
};
