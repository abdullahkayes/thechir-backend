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
        if (!Schema::hasColumn('stripe_orders', 'status')) {
            Schema::table('stripe_orders', function (Blueprint $table) {
                $table->integer('status')->default(1)->after('coupon');
            });
            echo "Added status column to stripe_orders table\n";
        } else {
            echo "status column already exists in stripe_orders table\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('stripe_orders', 'status')) {
            Schema::table('stripe_orders', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            echo "Removed status column from stripe_orders table\n";
        } else {
            echo "status column does not exist in stripe_orders table\n";
        }
    }
};
