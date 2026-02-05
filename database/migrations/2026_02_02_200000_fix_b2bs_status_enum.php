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
        // Fix the status column in b2bs table
        $columnInfo = DB::select("SELECT column_name, column_type FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'b2bs' AND column_name = 'status'");

        if (!empty($columnInfo)) {
            // Drop and recreate the enum with correct values
            DB::statement('ALTER TABLE b2bs DROP COLUMN status');

            // Add status column with correct ENUM values
            DB::statement("ALTER TABLE b2bs ADD COLUMN status ENUM('pending', 'approved') DEFAULT 'pending'");
        } else {
            // Column doesn't exist, add it
            DB::statement("ALTER TABLE b2bs ADD COLUMN status ENUM('pending', 'approved') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE b2bs DROP COLUMN status');
    }
};
