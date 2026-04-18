<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_home_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->integer('rated_user_id');
            $table->foreign('rated_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->integer('rating');

            $table->unique(['user_id', 'rated_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_home_ratings');
    }
};
