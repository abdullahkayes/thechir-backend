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
            $table->integer('coustomer_id');
            $table->integer('sub_total');
            $table->integer('total');
            $table->integer('discount');
            $table->integer('payment_method');
            $table->string('coupon')->nullable();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('street');
            $table->string('apartment');
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
