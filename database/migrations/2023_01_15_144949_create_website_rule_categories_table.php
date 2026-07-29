<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_rule_categories', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('description');
            $table->string('badge');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_rule_categories');
    }
};
