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
        Schema::table('billings', function (Blueprint $table) {
            // Add reseller_id column if it doesn't exist
            if (!Schema::hasColumn('billings', 'reseller_id')) {
                $table->unsignedBigInteger('reseller_id')->nullable()->after('coustomer_id');
            }
            
            // Add b2b_id column if it doesn't exist
            if (!Schema::hasColumn('billings', 'b2b_id')) {
                $table->unsignedBigInteger('b2b_id')->nullable()->after('reseller_id');
            }
            
            // Add distributer_id column if it doesn't exist
            if (!Schema::hasColumn('billings', 'distributer_id')) {
                $table->unsignedBigInteger('distributer_id')->nullable()->after('b2b_id');
            }
            
            // Add amazon_id column if it doesn't exist
            if (!Schema::hasColumn('billings', 'amazon_id')) {
                $table->unsignedBigInteger('amazon_id')->nullable()->after('distributer_id');
            }
            
            // Add user_id column for general users if it doesn't exist
            if (!Schema::hasColumn('billings', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('amazon_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn(['reseller_id', 'b2b_id', 'distributer_id', 'amazon_id', 'user_id']);
        });
    }
};
