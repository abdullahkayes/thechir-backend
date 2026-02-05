<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing status column and recreate with correct ENUM values
        // First check if it exists and has the wrong type
        try {
            DB::statement("ALTER TABLE `distributers` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        } catch (\Exception $e) {
            // If MODIFY fails, try to drop and recreate
            try {
                DB::statement("ALTER TABLE `distributers` DROP COLUMN `status`");
                DB::statement("ALTER TABLE `distributers` ADD COLUMN `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' AFTER `password`");
            } catch (\Exception $e2) {
                // Ignore if column doesn't exist or other issues
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We'll keep the fixed column, no rollback needed
    }
};
