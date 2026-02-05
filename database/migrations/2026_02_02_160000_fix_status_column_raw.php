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
        // Use raw SQL to change the column to proper ENUM
        // First check current column type
        $columnInfo = DB::select("SELECT column_name, column_type FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'resellers' AND column_name = 'status'");

        if (!empty($columnInfo)) {
            // Drop the column and recreate with correct ENUM
            DB::statement('ALTER TABLE resellers DROP COLUMN status');

            // Add status column with correct ENUM values
            DB::statement("ALTER TABLE resellers ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' AFTER venmo_zelle_id");
        } else {
            // Column doesn't exist, add it
            DB::statement("ALTER TABLE resellers ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' AFTER venmo_zelle_id");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE resellers DROP COLUMN status');
    }
};
