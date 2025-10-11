<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibleToCameraWebTable extends Migration
{
    public function up()
    {
        Schema::table('camera_web', function (Blueprint $table) {
            $table->boolean('visible')->default(true);
        });
    }

    public function down()
    {
        Schema::table('camera_web', function (Blueprint $table) {
            $table->dropColumn('visible');
        });
    }
}
