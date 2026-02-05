<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('distributor_points', function (Blueprint $table) {
            $table->text('google_maps_api_key')->nullable()->after('is_active');
            $table->text('locationiq_api_key')->nullable()->after('google_maps_api_key');
        });
    }

    public function down()
    {
        Schema::table('distributor_points', function (Blueprint $table) {
            $table->dropColumn(['google_maps_api_key', 'locationiq_api_key']);
        });
    }
};
