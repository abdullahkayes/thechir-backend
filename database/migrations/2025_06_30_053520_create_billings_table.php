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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->integer('coustomer_id')->nullable();
            $table->unsignedBigInteger('reseller_id')->nullable();
            $table->unsignedBigInteger('b2b_id')->nullable();
            $table->unsignedBigInteger('distributer_id')->nullable();
            $table->unsignedBigInteger('amazon_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('street');
            $table->string('apartment')->nullable();
            $table->string('city');
            $table->string('phone');
            $table->string('email');
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
        Schema::dropIfExists('billings');
    }
};
