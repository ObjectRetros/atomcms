<?php

use App\Models\Home\HomeCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(HomeCategory::class)->nullable()->constrained()->nullOnDelete();
            $table->char('type', 1);
            $table->integer('order')->default(0);
            $table->string('name');
            $table->string('image');
            $table->integer('price');
            $table->integer('currency_type')->default(-1);
            $table->boolean('enabled')->default(true);
            $table->integer('limit')->nullable();
            $table->integer('total_bought')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_items');
    }
};
