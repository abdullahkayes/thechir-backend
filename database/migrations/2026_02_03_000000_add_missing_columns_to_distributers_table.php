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
        // Add missing columns to distributers table
        Schema::table('distributers', function (Blueprint $table) {
            if (!Schema::hasColumn('distributers', 'license_number')) {
                $table->string('license_number')->after('email')->nullable();
            }
            if (!Schema::hasColumn('distributers', 'company_name')) {
                $table->string('company_name')->after('license_number')->nullable();
            }
            if (!Schema::hasColumn('distributers', 'address')) {
                $table->text('address')->after('company_name')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributers', function (Blueprint $table) {
            $table->dropColumn(['license_number', 'company_name', 'address']);
        });
    }
};
