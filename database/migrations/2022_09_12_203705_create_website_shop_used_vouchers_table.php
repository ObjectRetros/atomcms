<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('website_shop_used_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('code_id');
            $table->integer('user_id')->index();
            $table->string('ip_address')->index();
            $table->timestamps();

            $table->foreign('code_id')->references('id')->on('website_shop_vouchers')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_shop_used_vouchers');
    }
};