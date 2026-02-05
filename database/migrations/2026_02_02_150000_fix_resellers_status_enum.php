<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check current status column type and fix ENUM values
        $columnType = DB::select("SELECT column_type FROM information_schema.columns WHERE table_name = 'resellers' AND column_name = 'status'");

        if (!empty($columnType)) {
            // Drop and recreate the enum with correct values
            Schema::table('resellers', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        Schema::table('resellers', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('venmo_zelle_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resellers', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
