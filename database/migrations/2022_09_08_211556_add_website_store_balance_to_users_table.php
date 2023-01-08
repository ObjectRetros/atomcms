<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasColumn('users', 'website_store_balance')) {
            Schema::dropColumns('users', 'website_store_balance');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('website_store_balance')->default(0)->after('pixels');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('website_store_balance');
        });
    }
};