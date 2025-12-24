<?php

namespace Database\Seeders;

use App\Models\Shop\WebsiteShopCategory;
use App\Models\WebsiteShopItem;
use App\Models\WebsiteShopPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WebsiteShopSeeder extends Seeder
{
    private array $categories = [
        'Furniture' => [
            'items' => [
                'Throne Chair' => 'throne.png',
                'HC Sofa' => 'hc_sofa.png',
                'Rare Ice Cream Machine' => 'ice_cream.png',
                'Dragons Lamp' => 'dragon_lamp.png',
                'Scratch Post' => 'scratch_post.png',
                'Splash Pool' => 'pool.png',
                'Telepods' => 'telepod.png',
                'Diving Board' => 'diving_board.png',
            ],
        ],
        'Rare Items' => [
            'items' => [
                'Golden Dragon' => 'golden_dragon.png',
                'Crystal Ball' => 'crystal_ball.png',
                'Champion Trophy' => 'trophy.png',
                'Ancient Tomb' => 'tomb.png',
                'Magic Cauldron' => 'cauldron.png',
            ],
        ],
        'Room Effects' => [
            'items' => [
                'Nebula Background' => 'nebula.png',
                'Rain Effect' => 'rain.png',
                'Snow Machine' => 'snow.png',
                'Disco Lights' => 'disco.png',
            ],
        ],
    ];

    private array $packageNames = [
        'Royal Collection',
        'Dragon Master Bundle',
        'Party Paradise Pack',
        'Rare Treasures',
        'Magical Mystery Box',
        'Ultimate HC Bundle',
        'Summer Splash Collection',
        'Winter Wonderland Set',
        'VIP Luxury Package',
        'Haunted Manor Bundle',
    ];

    public function run(): void
    {
        // Create categories and items
        foreach ($this->categories as $categoryName => $data) {
            $category = WebsiteShopCategory::updateOrCreate(['name' => $categoryName,], ['slug' => Str::slug($categoryName)]);

            foreach ($data['items'] as $itemName => $image) {
                WebsiteShopItem::create([
                    'name' => $itemName,
                    'image' => $image,
                    'is_active' => true,
                ]);
            }
        }

        // Create packages
        $categories = WebsiteShopCategory::all();
        $items = WebsiteShopItem::all();

        foreach ($this->packageNames as $index => $packageName) {
            $package = WebsiteShopPackage::create([
                'name' => $packageName,
                'description' => fake()->text(100),
                'price' => fake()->numberBetween(99, 9999),
                'min_rank' => fake()->boolean(30) ? fake()->numberBetween(1, 5) : null,
                'max_rank' => fake()->boolean(20) ? fake()->numberBetween(6, 10) : null,
                'limit_per_user' => fake()->boolean(40) ? fake()->numberBetween(1, 5) : null,
                'stock' => fake()->boolean(50) ? fake()->numberBetween(10, 100) : null,
                'available_from' => fake()->boolean(70) ? now() : null,
                'available_to' => fake()->boolean(30) ? now()->addDays(fake()->numberBetween(1, 30)) : null,
                'website_shop_category_id' => $categories->random()->id,
            ]);

            // Attach random items to package
            $randomItems = $items->random(fake()->numberBetween(2, 6))
                ->mapWithKeys(function ($item) {
                    return [$item->id => ['quantity' => fake()->numberBetween(1, 3)]];
                })
                ->toArray();

            $package->items()->attach($randomItems);
        }
    }
}
