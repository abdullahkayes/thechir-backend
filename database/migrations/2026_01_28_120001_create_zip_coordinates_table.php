<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table caches ZIP code coordinates permanently to avoid repeated API calls.
     */
    public function up(): void
    {
        Schema::create('zip_coordinates', function (Blueprint $table) {
            $table->string('zip', 10)->primary();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();

            // Index for fast lookups
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zip_coordinates');
    }
};
