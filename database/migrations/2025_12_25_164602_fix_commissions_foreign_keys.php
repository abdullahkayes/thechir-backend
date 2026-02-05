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
        Schema::table('commissions', function (Blueprint $table) {
            // Drop existing foreign key that references wrong table
            $table->dropForeign(['reseller_id']);

            // Recreate foreign key with correct table reference
            $table->foreign('reseller_id')->references('id')->on('resellers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            // Drop the corrected foreign key
            $table->dropForeign(['reseller_id']);

            // Recreate with original (incorrect) reference for rollback
            $table->foreign('reseller_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
