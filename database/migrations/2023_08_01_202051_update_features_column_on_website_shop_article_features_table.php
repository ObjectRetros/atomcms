<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('website_shop_article_features')->truncate();

        Schema::table('website_shop_article_features', function (Blueprint $table) {
            $table->renameColumn('features', 'content');
        });

        Schema::table('website_shop_article_features', function (Blueprint $table) {
            $table->string('content')->change();
        });
    }

    public function down(): void
    {
        Schema::table('website_shop_article_features', function (Blueprint $table) {
            //
        });
    }
};
