<?php

namespace Database\Seeders;

use App\Models\Shop\WebsiteShopCategory;
use Illuminate\Database\Seeder;

class WebsiteShopCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'VIP',
                'slug' => 'vip',
                'icon' => asset('assets/images/shop/category-icon.png'),
            ],
            [
                'name' => 'Currency',
                'slug' => 'currency',
                'icon' => asset('assets/images/shop/category-icon.png'),
            ],
            [
                'name' => 'Badges',
                'slug' => 'badges',
                'icon' => asset('assets/images/shop/category-icon.png'),
            ],
            [
                'name' => 'Rares',
                'slug' => 'rares',
                'icon' => asset('assets/images/shop/category-icon.png'),
            ],
        ];

        WebsiteShopCategory::upsert($categories, ['slug'], ['name', 'icon']);
    }
}
