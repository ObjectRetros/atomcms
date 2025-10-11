<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_used_shop_vouchers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->unsignedBigInteger('voucher_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('voucher_id')->references('id')->on('website_shop_vouchers')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_used_shop_vouchers');
    }
};
