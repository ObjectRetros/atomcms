<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_shop_articles', function (Blueprint $table) {
            $table->renameColumn('icon', 'icon_url');
        });
    }

    public function down(): void
    {
        Schema::table('website_shop_articles', function (Blueprint $table) {
            $table->renameColumn('icon_url', 'icon');
        });
    }
};
