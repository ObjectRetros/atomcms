<?php

use App\Models\WebsiteShopItem;
use App\Models\WebsiteShopPackage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('website_shop_package_items', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(WebsiteShopPackage::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(WebsiteShopItem::class)->constrained()->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(1);

            $table->timestamps();

            $table->unique(['website_shop_package_id', 'website_shop_item_id'], 'package_item_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_shop_package_items');
    }
};
