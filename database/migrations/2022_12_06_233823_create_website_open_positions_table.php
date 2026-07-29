<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_open_positions', function (Blueprint $table) {
            $table->id();
            $table->integer('permission_id');
            $table->string('description');
            $table->timestamp('apply_from')->nullable();
            $table->timestamp('apply_to')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_open_positions');
    }
};
