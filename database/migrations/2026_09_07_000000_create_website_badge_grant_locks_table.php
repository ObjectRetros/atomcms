<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_badge_grant_locks', function (Blueprint $table): void {
            $table->char('lock_key', 64)->primary();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_badge_grant_locks');
    }
};
