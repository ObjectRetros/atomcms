<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('website_shop_product_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_product_id');
            $table->longText('content');

            $table->foreign('shop_product_id')->references('id')->on('website_shop_products')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_shop_product_features');
    }
};