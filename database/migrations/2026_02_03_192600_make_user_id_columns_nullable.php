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
        // Make columns nullable in stripe_orders
        Schema::table('stripe_orders', function (Blueprint $table) {
            if (Schema::hasColumn('stripe_orders', 'coustomer_id')) {
                $table->unsignedBigInteger('coustomer_id')->nullable()->change();
            }
            if (Schema::hasColumn('stripe_orders', 'reseller_id')) {
                $table->unsignedBigInteger('reseller_id')->nullable()->change();
            }
            if (Schema::hasColumn('stripe_orders', 'b2b_id')) {
                $table->unsignedBigInteger('b2b_id')->nullable()->change();
            }
            if (Schema::hasColumn('stripe_orders', 'distributer_id')) {
                $table->unsignedBigInteger('distributer_id')->nullable()->change();
            }
            if (Schema::hasColumn('stripe_orders', 'amazon_id')) {
                $table->unsignedBigInteger('amazon_id')->nullable()->change();
            }
        });

        // Make columns nullable in paypal_orders
        Schema::table('paypal_orders', function (Blueprint $table) {
            if (Schema::hasColumn('paypal_orders', 'coustomer_id')) {
                $table->unsignedBigInteger('coustomer_id')->nullable()->change();
            }
            if (Schema::hasColumn('paypal_orders', 'reseller_id')) {
                $table->unsignedBigInteger('reseller_id')->nullable()->change();
            }
            if (Schema::hasColumn('paypal_orders', 'b2b_id')) {
                $table->unsignedBigInteger('b2b_id')->nullable()->change();
            }
            if (Schema::hasColumn('paypal_orders', 'distributer_id')) {
                $table->unsignedBigInteger('distributer_id')->nullable()->change();
            }
            if (Schema::hasColumn('paypal_orders', 'amazon_id')) {
                $table->unsignedBigInteger('amazon_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Make columns not nullable in stripe_orders
        Schema::table('stripe_orders', function (Blueprint $table) {
            if (Schema::hasColumn('stripe_orders', 'coustomer_id')) {
                $table->unsignedBigInteger('coustomer_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('stripe_orders', 'reseller_id')) {
                $table->unsignedBigInteger('reseller_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('stripe_orders', 'b2b_id')) {
                $table->unsignedBigInteger('b2b_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('stripe_orders', 'distributer_id')) {
                $table->unsignedBigInteger('distributer_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('stripe_orders', 'amazon_id')) {
                $table->unsignedBigInteger('amazon_id')->nullable(false)->change();
            }
        });

        // Make columns not nullable in paypal_orders
        Schema::table('paypal_orders', function (Blueprint $table) {
            if (Schema::hasColumn('paypal_orders', 'coustomer_id')) {
                $table->unsignedBigInteger('coustomer_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('paypal_orders', 'reseller_id')) {
                $table->unsignedBigInteger('reseller_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('paypal_orders', 'b2b_id')) {
                $table->unsignedBigInteger('b2b_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('paypal_orders', 'distributer_id')) {
                $table->unsignedBigInteger('distributer_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('paypal_orders', 'amazon_id')) {
                $table->unsignedBigInteger('amazon_id')->nullable(false)->change();
            }
        });
    }
};
