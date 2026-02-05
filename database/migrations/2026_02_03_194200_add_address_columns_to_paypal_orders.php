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
            // Add all the address-related columns that are missing
            if (!Schema::hasColumn('paypal_orders', 'name')) {
                $table->string('name')->after('status');
            }
            
            if (!Schema::hasColumn('paypal_orders', 'company')) {
                $table->string('company')->nullable()->after('name');
            }
            
            if (!Schema::hasColumn('paypal_orders', 'street')) {
                $table->text('street')->after('company');
            }
            
            if (!Schema::hasColumn('paypal_orders', 'apartment')) {
                $table->text('apartment')->nullable()->after('street');
            }
            
            if (!Schema::hasColumn('paypal_orders', 'city')) {
                $table->string('city')->after('apartment');
            }
            
            if (!Schema::hasColumn('paypal_orders', 'phone')) {
                $table->string('phone')->after('city');
            }
            
            if (!Schema::hasColumn('paypal_orders', 'email')) {
                $table->string('email')->after('phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paypal_orders', function (Blueprint $table) {
            if (Schema::hasColumn('paypal_orders', 'name')) {
                $table->dropColumn('name');
            }
            
            if (Schema::hasColumn('paypal_orders', 'company')) {
                $table->dropColumn('company');
            }
            
            if (Schema::hasColumn('paypal_orders', 'street')) {
                $table->dropColumn('street');
            }
            
            if (Schema::hasColumn('paypal_orders', 'apartment')) {
                $table->dropColumn('apartment');
            }
            
            if (Schema::hasColumn('paypal_orders', 'city')) {
                $table->dropColumn('city');
            }
            
            if (Schema::hasColumn('paypal_orders', 'phone')) {
                $table->dropColumn('phone');
            }
            
            if (Schema::hasColumn('paypal_orders', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
