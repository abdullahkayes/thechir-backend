<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            // Add reference_type column if it doesn't exist
            if (!Schema::hasColumn('inventory_movements', 'reference_type')) {
                $table->string('reference_type')->nullable()->after('total_value');
            }
            // Add reference_id column if it doesn't exist
            if (!Schema::hasColumn('inventory_movements', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            }
            // Add reason column if it doesn't exist
            if (!Schema::hasColumn('inventory_movements', 'reason')) {
                $table->text('reason')->nullable()->after('reference_id');
            }
            // Add notes column if it doesn't exist
            if (!Schema::hasColumn('inventory_movements', 'notes')) {
                $table->text('notes')->nullable()->after('reason');
            }
            // Add user_id column if it doesn't exist
            if (!Schema::hasColumn('inventory_movements', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('notes')->constrained()->onDelete('set null');
            }
            // Add stock_detail_id column if it doesn't exist
            if (!Schema::hasColumn('inventory_movements', 'stock_detail_id')) {
                $table->foreignId('stock_detail_id')->nullable()->after('id')->constrained()->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn([
                'reference_type', 'reference_id', 'reason', 'notes', 'user_id', 'stock_detail_id'
            ]);
        });
    }
};
