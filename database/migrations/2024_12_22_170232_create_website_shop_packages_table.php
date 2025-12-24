<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('website_shop_packages', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedInteger('min_rank')->nullable()->comment('Minimum rank required to purchase');
            $table->unsignedInteger('max_rank')->nullable()->comment('Maximum rank required to purchase');
            $table->unsignedInteger('limit_per_user')->nullable()->comment('Limit the amount of times the user can purchase this package');
            $table->unsignedInteger('stock')->nullable()->comment('Limit the amount of times this package can be purchased');
            $table->unsignedInteger('price')->comment('Price in cents');

            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_to')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_shop_packages');
    }
};
