<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('website_shop_items', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('image');
            $table->string('type');
            $table->string('type_value')->comment('Can be anything e.g. item id, currency type & amount (5:100 = 100 diamonds), or badge code');
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_shop_items');
    }
};
