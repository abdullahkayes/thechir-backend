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
        Schema::table('distributor_points', function (Blueprint $table) {
            $table->integer('api_key_index')->nullable()->after('locationiq_api_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributor_points', function (Blueprint $table) {
            $table->dropColumn('api_key_index');
        });
    }
};
