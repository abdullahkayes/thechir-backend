<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, set any reseller_ids that don't exist in resellers table to NULL
        DB::statement('UPDATE billings b SET reseller_id = NULL WHERE reseller_id IS NOT NULL AND reseller_id NOT IN (SELECT id FROM resellers)');
        
        // Drop foreign key - ignore if it doesn't exist
        try {
            DB::statement('ALTER TABLE billings DROP FOREIGN KEY billings_reseller_id_foreign');
        } catch (\Exception $e) {
            // Foreign key doesn't exist, continue
        }
        
        // Now add the correct foreign key
        DB::statement('ALTER TABLE billings ADD CONSTRAINT billings_reseller_id_foreign FOREIGN KEY (reseller_id) REFERENCES resellers(id) ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropForeign(['reseller_id']);
            
            // Revert to original (incorrect) foreign key
            $table->foreign('reseller_id')->references('id')->on('users');
        });
    }
};
