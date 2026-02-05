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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained('resellers')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->decimal('amount', 8, 2);
            $table->enum('status', ['pending', 'available', 'paid', 'used'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('used_in_order_id')->nullable();
            $table->timestamps();

            // Add foreign key constraint for used_in_order_id
            $table->foreign('used_in_order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
