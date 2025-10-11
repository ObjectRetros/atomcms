<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_shop_article_features', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('article_id');
            $table->json('features');

            $table->timestamps();

            $table->foreign('article_id')->references('id')->on('website_shop_articles')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_shop_article_features');
    }
};
