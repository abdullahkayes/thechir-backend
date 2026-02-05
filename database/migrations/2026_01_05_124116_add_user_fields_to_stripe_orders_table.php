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
        Schema::table('stripe_orders', function (Blueprint $table) {
            $table->integer('coustomer_id')->nullable()->change();
            $table->integer('reseller_id')->nullable();
            $table->integer('b2b_id')->nullable();
            $table->integer('distributer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stripe_orders', function (Blueprint $table) {
            $table->dropColumn(['reseller_id', 'b2b_id', 'distributer_id']);
            $table->integer('coustomer_id')->nullable(false)->change();
        });
    }
};
