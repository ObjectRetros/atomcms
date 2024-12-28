<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('housekeeping_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('permission')->unique();
            $table->integer('min_rank');
            $table->string('description')->comment('Describes the permissions')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('housekeeping_permissions');
    }
};