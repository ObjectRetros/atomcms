<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_installation', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('step')->default(0)->nullable();
            $table->boolean('completed')->default(false);
            $table->string('installation_key')->nullable();
            $table->string('user_ip')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_installation');
    }
};
