<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_rare_value_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('badge');
            $table->unsignedInteger('priority')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_rare_value_categories');
    }
};
