<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPageDataColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pages')) {
            return;
        }

        $columns = ['visitors', 'views', 'duration', 'bounce'];
        $existingColumns = [];

        foreach ($columns as $column) {
            if (Schema::hasColumn('pages', $column)) {
                $existingColumns[] = $column;
            }
        }

        if (count($existingColumns) === count($columns)) {
            return; // All columns exist, skip migration
        }

        Schema::table('pages', function (Blueprint $table) use ($existingColumns) {
            if (!in_array('visitors', $existingColumns)) {
                $table->integer('visitors')->default(0);
            }
            if (!in_array('views', $existingColumns)) {
                $table->integer('views')->default(0);
            }
            if (!in_array('duration', $existingColumns)) {
                $table->integer('duration')->default(0);
            }
            if (!in_array('bounce', $existingColumns)) {
                $table->integer('bounce')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['visitors', 'views', 'duration', 'bounce']);
        });
    }
}
