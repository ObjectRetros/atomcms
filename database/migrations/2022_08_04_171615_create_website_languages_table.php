<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_languages', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 8)->unique();
            $table->string('language')->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_languages');
    }
};
