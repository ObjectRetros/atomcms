<?php

use App\Models\Shop\WebsiteShopCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_shop_packages', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(WebsiteShopCategory::class)->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('price')->comment('Price in cents');

            $table->unsignedInteger('min_rank')->nullable()->comment('Minimum rank required to purchase');
            $table->unsignedInteger('max_rank')->nullable()->comment('Maximum rank required to purchase');
            $table->unsignedInteger('limit_per_user')->nullable()->comment('Max purchases per user');
            $table->unsignedInteger('stock')->nullable()->comment('Total available stock');
            $table->boolean('is_giftable')->default(true);

            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_to')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_shop_packages');
    }
};
