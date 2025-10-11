<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_user_guestbooks', function (Blueprint $table) {
            $table->id();

            $table->integer('profile_id');
            $table->integer('user_id');
            $table->string('message');

            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_user_guestbooks');
    }
};
