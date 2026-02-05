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
        // Add missing price columns to slider_videos table
        $columns = [
            'wholesale_price' => 'DECIMAL(10, 2)',
            'reseller_price' => 'DECIMAL(10, 2)',
            'distributer_price' => 'DECIMAL(10, 2)',
            'amazon_price' => 'DECIMAL(10, 2)',
        ];

        foreach ($columns as $column => $definition) {
            try {
                if (!Schema::hasColumn('slider_videos', $column)) {
                    DB::statement("ALTER TABLE `slider_videos` ADD COLUMN `{$column}` {$definition} NULL");
                }
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
        DB::statement("ALTER TABLE `slider_videos` DROP COLUMN IF EXISTS `wholesale_price`");
        DB::statement("ALTER TABLE `slider_videos` DROP COLUMN IF EXISTS `reseller_price`");
        DB::statement("ALTER TABLE `slider_videos` DROP COLUMN IF EXISTS `distributer_price`");
        DB::statement("ALTER TABLE `slider_videos` DROP COLUMN IF EXISTS `amazon_price`");
    }
};
