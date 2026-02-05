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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->decimal('sub_total', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();
            $table->integer('coustomer_id')->nullable();
            $table->unsignedBigInteger('reseller_id')->nullable();
            $table->unsignedBigInteger('b2b_id')->nullable();
            $table->unsignedBigInteger('distributer_id')->nullable();
            $table->unsignedBigInteger('amazon_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payer_id')->nullable();
            $table->string('payer_email')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->string('status')->default('pending');
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('street');
            $table->string('apartment')->nullable();
            $table->string('city');
            $table->string('phone');
            $table->string('email');
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->integer('payment_method');
            $table->string('coupon')->nullable();
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
        Schema::dropIfExists('order_products');
    }
};
