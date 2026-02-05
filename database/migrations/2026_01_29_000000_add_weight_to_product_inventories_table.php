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
            $table->decimal('weight_grams', 10, 2)->nullable()->after('quantity');
            $table->string('weight_unit', 10)->default('g')->after('weight_grams');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_inventories', function (Blueprint $table) {
            $table->dropColumn(['weight_grams', 'weight_unit']);
        });
    }
};
