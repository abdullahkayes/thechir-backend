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
        Schema::table('subcategories', function (Blueprint $table) {
            if (Schema::hasColumn('subcategories', 'subcategory_image')) {
                $table->dropColumn('subcategory_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table ('subcategories', function (Blueprint $table) {
            if (!Schema::hasColumn('subcategories', 'subcategory_image')) {
                $table->string('subcategory_image')->nullable();
            }
        });
    }
};
