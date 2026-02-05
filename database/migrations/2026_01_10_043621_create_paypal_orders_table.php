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
        Schema::create('paypal_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->decimal('sub_total', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();
            $table->unsignedBigInteger('coustomer_id')->nullable();
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
            $table->text('street');
            $table->text('apartment')->nullable();
            $table->string('city');
            $table->string('phone');
            $table->string('email');
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('coupon')->nullable();
            $table->timestamps();

            // Add foreign key constraints
            $table->foreign('coustomer_id')->references('id')->on('coustomers')->onDelete('set null');
            $table->foreign('reseller_id')->references('id')->on('resellers')->onDelete('set null');
            $table->foreign('b2b_id')->references('id')->on('b2bs')->onDelete('set null');
            $table->foreign('distributer_id')->references('id')->on('distributers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paypal_orders');
    }
};
