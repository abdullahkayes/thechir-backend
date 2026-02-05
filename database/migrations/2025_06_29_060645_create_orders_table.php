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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->integer('coustomer_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reseller_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('b2b_id')->nullable()->constrained('b2bs')->onDelete('set null');
            $table->unsignedBigInteger('distributer_id')->nullable();
            $table->unsignedBigInteger('amazon_id')->nullable();
            $table->decimal('sub_total', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('payment_method');
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->string('coupon')->nullable();
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->integer('quantity')->nullable();
            $table->decimal('sell_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('balance_used', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid'])->default('paid');
            $table->timestamps();

            $table->foreign('distributer_id')->references('id')->on('distributers')->onDelete('cascade');
            $table->foreign('amazon_id')->references('id')->on('amazons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
