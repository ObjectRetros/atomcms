<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('website_articles', 'deleted_at')) {
            Schema::table('website_articles', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('website_articles', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
