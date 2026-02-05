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
        Schema::table('carts', function (Blueprint $table) {
            $table->integer('reseller_id')->nullable()->after('coustomer_id');
            $table->integer('b2b_id')->nullable()->after('reseller_id');
            $table->integer('distributer_id')->nullable()->after('b2b_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['reseller_id', 'b2b_id', 'distributer_id']);
        });
    }
};
