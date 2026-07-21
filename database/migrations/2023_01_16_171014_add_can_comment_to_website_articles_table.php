<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('website_articles', 'can_comment')) {
            Schema::table('website_articles', function (Blueprint $table) {
                $table->boolean('can_comment')->default(true)->after('image');
            });
        }
    }

    public function down(): void
    {
        Schema::table('website_articles', function (Blueprint $table) {
            $table->dropColumn('can_comment');
        });
    }
};
