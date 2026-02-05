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
        Schema::table('slider_videos', function (Blueprint $table) {
            $table->decimal('wholesale_price', 10, 2)->nullable()->after('discount_price');
            $table->decimal('reseller_price', 10, 2)->nullable()->after('wholesale_price');
            $table->decimal('distributer_price', 10, 2)->nullable()->after('reseller_price');
            $table->decimal('amazon_price', 10, 2)->nullable()->after('distributer_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slider_videos', function (Blueprint $table) {
            $table->dropColumn(['wholesale_price', 'reseller_price', 'distributer_price', 'amazon_price']);
        });
    }
};
