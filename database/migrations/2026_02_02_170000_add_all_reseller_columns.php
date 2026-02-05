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
        // Add all columns that might be missing from the resellers table
        $columns = [
            'name' => 'VARCHAR(255) NULL',
            'email' => 'VARCHAR(255) NULL',
            'phone' => 'VARCHAR(255) NULL',
            'venmo_zelle_id' => 'VARCHAR(255) NULL',
            'ref_link' => 'VARCHAR(255) NULL',
            'commission_percentage' => 'DECIMAL(5,2) DEFAULT 5.00',
        ];

        foreach ($columns as $column => $definition) {
            $exists = DB::select("SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'resellers' AND column_name = '{$column}'");

            if (empty($exists)) {
                DB::statement("ALTER TABLE resellers ADD COLUMN {$column} {$definition}");
            }
        }

        // Ensure status column exists with correct ENUM
        $statusExists = DB::select("SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'resellers' AND column_name = 'status'");

        if (empty($statusExists)) {
            DB::statement("ALTER TABLE resellers ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the columns (optional)
    }
};
