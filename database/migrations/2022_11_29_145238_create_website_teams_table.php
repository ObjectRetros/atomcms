<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_teams', function (Blueprint $table) {
            $table->id();
            $table->string('rank_name')->unique();
            $table->boolean('hidden_rank')->default(false);
            $table->string('badge')->nullable();
            $table->string('job_description')->nullable();
            $table->string('staff_color')->default('#327fa8');
            $table->string('staff_background')->default('staff-bg.png');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_teams');
    }
};
