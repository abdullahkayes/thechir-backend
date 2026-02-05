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
        // Add missing columns to commissions table
        $columns = [
            'reseller_id' => 'BIGINT UNSIGNED NULL',
            'order_id' => 'BIGINT UNSIGNED NULL',
            'amount' => 'DECIMAL(8,2) NULL',
            'status' => "ENUM('pending', 'available', 'paid') DEFAULT 'pending'",
            'paid_at' => 'TIMESTAMP NULL',
            'created_at' => 'TIMESTAMP NULL',
            'updated_at' => 'TIMESTAMP NULL',
        ];

        foreach ($columns as $column => $definition) {
            $exists = DB::select("SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'commissions' AND column_name = '{$column}'");

            if (empty($exists)) {
                DB::statement("ALTER TABLE commissions ADD COLUMN {$column} {$definition}");
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
