<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('website_shop_categories', function (Blueprint $table) {
            $table->boolean('is_active')->after('icon')->default(true);
        });
    }

    public function down()
    {
        Schema::table('website_shop_categories', function (Blueprint $table) {
            //
        });
    }
};
