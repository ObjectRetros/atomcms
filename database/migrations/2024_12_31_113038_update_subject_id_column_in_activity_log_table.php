<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSubjectIdColumnInActivityLogTable extends Migration
{
    public function up()
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->string('subject_id')->change();
        });
    }

    public function down()
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->integer('subject_id')->change();
        });
    }
}
