<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('camera_web') || Schema::hasColumn('camera_web', 'visible')) {
            return;
        }

        Schema::table('camera_web', function (Blueprint $table) {
            $table->boolean('visible')->default(true);
        });
    }

    public function down()
    {
        if (! Schema::hasTable('camera_web') || ! Schema::hasColumn('camera_web', 'visible')) {
            return;
        }

        Schema::table('camera_web', function (Blueprint $table) {
            $table->dropColumn('visible');
        });
    }
};
