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
        // Add missing columns to b2bs table
        $columns = [
            'name' => 'VARCHAR(255) NULL',
            'email' => 'VARCHAR(255) NULL',
            'email_verified_at' => 'TIMESTAMP NULL',
            'password' => 'VARCHAR(255) NULL',
            'business_name' => 'VARCHAR(255) NULL',
            'ein' => 'VARCHAR(255) NULL',
            'resale_certificate_path' => 'VARCHAR(255) NULL',
            'shipping_address' => 'TEXT NULL',
            'status' => "ENUM('pending', 'approved') DEFAULT 'pending'",
            'ref_id' => 'VARCHAR(255) NULL',
            'remember_token' => 'VARCHAR(100) NULL',
            'created_at' => 'TIMESTAMP NULL',
            'updated_at' => 'TIMESTAMP NULL',
        ];

        foreach ($columns as $column => $definition) {
            $exists = DB::select("SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'b2bs' AND column_name = '{$column}'");

            if (empty($exists)) {
                DB::statement("ALTER TABLE b2bs ADD COLUMN {$column} {$definition}");
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
