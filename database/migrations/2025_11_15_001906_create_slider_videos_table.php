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
        Schema::create('slider_videos', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->nullable();
            $table->string('video');
            $table->string('thumbnail');
            $table->string('product_image');
            $table->string('name');
            $table->decimal('price');
            $table->decimal('discount_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slider_videos');
    }
};
