<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_home_items', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('home_item_id');
            $table->foreign('home_item_id')->references('id')->on('home_items')->cascadeOnDelete();
            $table->integer('x')->default(0);
            $table->integer('y')->default(0);
            $table->integer('z')->default(0);
            $table->boolean('placed')->default(false);
            $table->boolean('is_reversed')->default(false);
            $table->text('extra_data')->nullable();
            $table->string('theme', 15)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_home_items');
    }
};
