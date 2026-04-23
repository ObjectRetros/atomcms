<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_shop_categories', function (Blueprint $table) {
            $table->boolean('is_active')->after('icon')->default(true);
            $table->unsignedInteger('sort_order')->after('icon')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('website_shop_categories', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'sort_order']);
        });
    }
};
