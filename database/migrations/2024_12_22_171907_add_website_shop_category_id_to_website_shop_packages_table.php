<?php

use App\Models\Shop\WebsiteShopCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('website_shop_packages', function (Blueprint $table) {
            $table->foreignIdFor(WebsiteShopCategory::class)->after('id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('website_shop_packages', function (Blueprint $table) {
            //
        });
    }
};
