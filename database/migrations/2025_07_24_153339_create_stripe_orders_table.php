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
        Schema::create('stripe_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->integer('coustomer_id')->nullable();
            $table->integer('reseller_id')->nullable();
            $table->integer('b2b_id')->nullable();
            $table->integer('distributer_id')->nullable();
            $table->integer('amazon_id')->nullable();
            $table->decimal('sub_total', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('coupon')->nullable();
            $table->string('status')->default('pending');
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('street');
            $table->string('apartment')->nullable();
            $table->string('city');
            $table->string('phone');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_orders');
    }
};
