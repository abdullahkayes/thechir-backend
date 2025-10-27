<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_tracking', function (Blueprint $table) {
            $table->id();

            // Link to the orders table
            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            // The numeric status (0, 1, 2, etc., same as in your order table status)
            $table->unsignedSmallInteger('status')->default(0);

            // The detailed message (e.g., "Package left facility in Texas")
            $table->string('description', 255);

            // The location where the status update occurred
            $table->string('location')->nullable();

            $table->timestamps(); // crucial for the timestamp field
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_tracking');
    }
};

