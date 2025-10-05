<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('website_shop_packages', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->after('id')->default(0);
        });
    }

    public function down()
    {
        Schema::table('website_shop_packages', function (Blueprint $table) {
            //
        });
    }
};
