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
        // Add missing columns to payout_requests table
        $columns = [
            'reseller_id' => 'BIGINT UNSIGNED NULL',
            'amount' => 'DECIMAL(10,2) NULL',
            'status' => "ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'",
            'notes' => 'TEXT NULL',
            'approved_at' => 'TIMESTAMP NULL',
            'approved_by' => 'BIGINT UNSIGNED NULL',
            'created_at' => 'TIMESTAMP NULL',
            'updated_at' => 'TIMESTAMP NULL',
        ];

        foreach ($columns as $column => $definition) {
            $exists = DB::select("SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'payout_requests' AND column_name = '{$column}'");

            if (empty($exists)) {
                DB::statement("ALTER TABLE payout_requests ADD COLUMN {$column} {$definition}");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
