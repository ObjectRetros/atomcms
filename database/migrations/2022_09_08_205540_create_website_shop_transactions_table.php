<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::dropIfExists('website_shop_transactions');


        Schema::create('website_shop_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('payment_id')->index();
            $table->string('status')->default('pending');
            $table->unsignedInteger('amount');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_shop_transactions');
    }
};