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
        Schema::table('orders', function (Blueprint $table) {
            // Drop existing foreign keys that reference wrong tables
            $table->dropForeign(['reseller_id']);
            $table->dropForeign(['b2b_id']);
            $table->dropForeign(['distributer_id']);

            // Recreate foreign keys with correct table references
            $table->foreign('reseller_id')->references('id')->on('resellers')->onDelete('set null');
            $table->foreign('b2b_id')->references('id')->on('b2bs')->onDelete('set null');
            $table->foreign('distributer_id')->references('id')->on('distributers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the corrected foreign keys
            $table->dropForeign(['reseller_id']);
            $table->dropForeign(['b2b_id']);
            $table->dropForeign(['distributer_id']);

            // Recreate with original (incorrect) references for rollback
            $table->foreign('reseller_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('b2b_id')->references('id')->on('b2bs')->onDelete('set null');
            $table->foreign('distributer_id')->references('id')->on('distributers')->onDelete('set null');
        });
    }
};
