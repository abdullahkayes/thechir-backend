<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add all missing columns to distributers table
        $columns = [
            'license_number' => 'VARCHAR(255)',
            'company_name' => 'VARCHAR(255)',
            'address' => 'TEXT',
            'password' => 'VARCHAR(255)',
            'status' => "ENUM(\'pending\', \'approved\', \'rejected\') DEFAULT \'pending\'",
            'email_verified_at' => 'TIMESTAMP NULL',
            'remember_token' => 'VARCHAR(100) NULL',
        ];

        foreach ($columns as $column => $definition) {
            try {
                DB::statement("ALTER TABLE `distributers` ADD COLUMN `{$column}` {$definition}");
            } catch (\Exception $e) {
                // Column may already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We'll drop non-essential columns only
        DB::statement("ALTER TABLE `distributers` DROP COLUMN IF EXISTS `license_number`");
        DB::statement("ALTER TABLE `distributers` DROP COLUMN IF EXISTS `company_name`");
        DB::statement("ALTER TABLE `distributers` DROP COLUMN IF EXISTS `address`");
    }
};
