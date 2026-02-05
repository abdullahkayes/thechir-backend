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
        // Change status column from integer to ENUM
        $columnInfo = DB::select("SELECT column_name, column_type FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'orders' AND column_name = 'status'");

        if (!empty($columnInfo)) {
            // Drop the integer status column and recreate as ENUM
            DB::statement('ALTER TABLE orders DROP COLUMN status');

            // Add status column with correct ENUM values
            DB::statement("ALTER TABLE orders ADD COLUMN status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending' AFTER payment_method");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE orders DROP COLUMN status');
    }
};
