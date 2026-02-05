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
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->decimal('wholesale_price', 10, 2)->nullable();
            $table->decimal('reseller_price', 10, 2)->nullable();
            $table->decimal('distributer_price', 10, 2)->nullable();
            $table->decimal('amazon_price', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
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
