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
            if (!Schema::hasColumn('stripe_orders', 'coustomer_id')) {
                $table->unsignedBigInteger('coustomer_id')->nullable()->after('order_id');
            }
            if (!Schema::hasColumn('stripe_orders', 'reseller_id')) {
                $table->unsignedBigInteger('reseller_id')->nullable()->after('coustomer_id');
            }
            if (!Schema::hasColumn('stripe_orders', 'b2b_id')) {
                $table->unsignedBigInteger('b2b_id')->nullable()->after('reseller_id');
            }
            if (!Schema::hasColumn('stripe_orders', 'distributer_id')) {
                $table->unsignedBigInteger('distributer_id')->nullable()->after('b2b_id');
            }
            if (!Schema::hasColumn('stripe_orders', 'amazon_id')) {
                $table->unsignedBigInteger('amazon_id')->nullable()->after('distributer_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stripe_orders', function (Blueprint $table) {
            if (Schema::hasColumn('stripe_orders', 'coustomer_id')) {
                $table->dropColumn('coustomer_id');
            }
            if (Schema::hasColumn('stripe_orders', 'reseller_id')) {
                $table->dropColumn('reseller_id');
            }
            if (Schema::hasColumn('stripe_orders', 'b2b_id')) {
                $table->dropColumn('b2b_id');
            }
            if (Schema::hasColumn('stripe_orders', 'distributer_id')) {
                $table->dropColumn('distributer_id');
            }
            if (Schema::hasColumn('stripe_orders', 'amazon_id')) {
                $table->dropColumn('amazon_id');
            }
        });
    }
};
