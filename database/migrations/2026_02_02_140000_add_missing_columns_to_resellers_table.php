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
        // Add missing columns to resellers table
        if (!Schema::hasColumn('resellers', 'name')) {
            Schema::table('resellers', function (Blueprint $table) {
                $table->string('name')->nullable()->after('id');
            });
        }

        if (!Schema::hasColumn('resellers', 'email')) {
            Schema::table('resellers', function (Blueprint $table) {
                $table->string('email')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('resellers', 'phone')) {
            Schema::table('resellers', function (Blueprint $table) {
                $table->string('phone')->nullable()->after('email');
            });
        }

        if (!Schema::hasColumn('resellers', 'venmo_zelle_id')) {
            Schema::table('resellers', function (Blueprint $table) {
                $table->string('venmo_zelle_id')->nullable()->after('phone');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resellers', function (Blueprint $table) {
            $table->dropColumn(['name', 'email', 'phone', 'venmo_zelle_id']);
        });
    }
};
