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
            $table->unsignedBigInteger('reseller_id')->nullable()->after('coustomer_id');
            $table->unsignedBigInteger('b2b_id')->nullable()->after('reseller_id');
            $table->foreign('reseller_id')->references('id')->on('resellers')->onDelete('cascade');
            $table->foreign('b2b_id')->references('id')->on('b2bs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['reseller_id']);
            $table->dropForeign(['b2b_id']);
            $table->dropColumn(['reseller_id', 'b2b_id']);
        });
    }
};