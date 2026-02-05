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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('coustomer_id')->nullable();
            $table->integer('color_id');
            $table->integer('size_id');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->decimal('sell_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->boolean('from_family_bundles')->default(false);
            $table->unsignedInteger('weight_grams')->default(500)->comment('Product weight in grams for shipping calculation');
            $table->unsignedBigInteger('reseller_id')->nullable();
            $table->unsignedBigInteger('b2b_id')->nullable();
            $table->unsignedBigInteger('distributer_id')->nullable();
            $table->unsignedBigInteger('amazon_id')->nullable();
            $table->timestamps();

            $table->foreign('reseller_id')->references('id')->on('resellers')->onDelete('cascade');
            $table->foreign('b2b_id')->references('id')->on('b2bs')->onDelete('cascade');
            $table->foreign('distributer_id')->references('id')->on('distributers')->onDelete('cascade');
            $table->foreign('amazon_id')->references('id')->on('amazons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
