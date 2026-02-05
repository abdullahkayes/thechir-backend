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
            // Add all missing columns based on the PayPalOrder model
            if (!Schema::hasColumn('paypal_orders', 'total')) {
                $table->decimal('total', 10, 2)->after('sub_total');
            }
            
            if (!Schema::hasColumn('paypal_orders', 'discount')) {
                $table->decimal('discount', 10, 2)->default(0)->after('total');
            }
            
            if (!Schema::hasColumn('paypal_orders', 'delivery_charge')) {
                $table->decimal('delivery_charge', 10, 2)->default(0)->after('discount');
            }
            
            if (!Schema::hasColumn('paypal_orders', 'payment_method')) {
                $table->integer('payment_method')->after('delivery_charge');
            }
            
            if (!Schema::hasColumn('paypal_orders', 'coupon')) {
                $table->string('coupon')->nullable()->after('payment_method');
            }
            
            if (!Schema::hasColumn('paypal_orders', 'status')) {
                $table->integer('status')->default(1)->after('coupon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paypal_orders', function (Blueprint $table) {
            if (Schema::hasColumn('paypal_orders', 'total')) {
                $table->dropColumn('total');
            }
            
            if (Schema::hasColumn('paypal_orders', 'discount')) {
                $table->dropColumn('discount');
            }
            
            if (Schema::hasColumn('paypal_orders', 'delivery_charge')) {
                $table->dropColumn('delivery_charge');
            }
            
            if (Schema::hasColumn('paypal_orders', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            
            if (Schema::hasColumn('paypal_orders', 'coupon')) {
                $table->dropColumn('coupon');
            }
            
            if (Schema::hasColumn('paypal_orders', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
