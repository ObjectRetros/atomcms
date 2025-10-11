<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_help_center_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('content');
            $table->unsignedInteger('position')->default(1);
            $table->string('image_url')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('button_color', 16)->default('#eeb425');
            $table->string('button_border_color', 16)->default('#facc15');
            $table->boolean('small_box')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_help_center_categories');
    }
};
