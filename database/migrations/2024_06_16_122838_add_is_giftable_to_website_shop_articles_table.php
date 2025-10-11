<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_shop_articles', function (Blueprint $table) {
            $table->boolean('is_giftable')->default(true)->after('position');
        });
    }
};
