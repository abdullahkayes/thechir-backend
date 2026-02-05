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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('venmo_zelle_id')->nullable();
            $table->string('unique_ref_id')->unique()->nullable();
            $table->string('ref_link')->nullable();
            $table->string('discount_code')->nullable();
            $table->string('business_name')->nullable();
            $table->string('ein')->nullable();
            $table->string('resale_certificate_path')->nullable();
            $table->text('shipping_address')->nullable();
            $table->enum('status', ['pending', 'approved'])->default('pending');
            $table->string('ref_id')->nullable(); // referrer's unique_ref_id
            $table->decimal('commission_percentage', 5, 2)->default(5.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'venmo_zelle_id', 'unique_ref_id', 'ref_link', 'discount_code', 'business_name', 'ein', 'resale_certificate_path', 'shipping_address', 'status', 'ref_id', 'commission_percentage']);
        });
    }
};
