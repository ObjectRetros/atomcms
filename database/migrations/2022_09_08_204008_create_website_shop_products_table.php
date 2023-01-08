<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('website_shop_products', function (Blueprint $table) {
            $table->id();
            $table->longText('data');
            $table->unsignedInteger('price')->default(9999);
            $table->unsignedInteger('percentage_off')->nullable();
            $table->timestamp('sale_start')->nullable();
            $table->timestamp('sale_end')->nullable();
            $table->string('type')->default('vip');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_shop_products');
    }
};