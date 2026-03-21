<?php

namespace Database\Seeders;

use App\Models\Shop\WebsiteShopCategory;
use App\Models\Shop\WebsiteShopItem;
use App\Models\Shop\WebsiteShopPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WebsiteShopSeeder extends Seeder
{
    /** @var array<string, array{type: string, type_value: string}> */
    private array $items = [
        'Throne Chair' => ['type' => 'furniture', 'type_value' => '1001'],
        'HC Sofa' => ['type' => 'furniture', 'type_value' => '1002'],
        'Rare Ice Cream Machine' => ['type' => 'furniture', 'type_value' => '1003'],
        'Dragons Lamp' => ['type' => 'furniture', 'type_value' => '1004'],
        'Telepods' => ['type' => 'furniture', 'type_value' => '1005'],
        '100 Credits' => ['type' => 'currency', 'type_value' => 'credits:100'],
        '50 Duckets' => ['type' => 'currency', 'type_value' => 'duckets:50'],
        '25 Diamonds' => ['type' => 'currency', 'type_value' => 'diamonds:25'],
        'VIP Badge' => ['type' => 'badge', 'type_value' => 'VIP01'],
        'VIP Rank' => ['type' => 'rank', 'type_value' => '2'],
    ];

    private array $categories = ['Furniture', 'Currency', 'VIP', 'Bundles'];

    public function run(): void
    {
        $categories = collect($this->categories)->mapWithKeys(function (string $name) {
            $category = WebsiteShopCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true],
            );

            return [$name => $category];
        });

        $items = collect($this->items)->map(function (array $data, string $name) {
            return WebsiteShopItem::updateOrCreate(
                ['name' => $name],
                [...$data, 'is_active' => true],
            );
        });

        $packageDefinitions = [
            [
                'name' => 'Starter Pack',
                'category' => 'Bundles',
                'price' => 499,
                'items' => ['100 Credits' => 1, 'HC Sofa' => 1],
            ],
            [
                'name' => 'VIP Package',
                'category' => 'VIP',
                'price' => 1999,
                'items' => ['VIP Rank' => 1, 'VIP Badge' => 1, '100 Credits' => 5],
            ],
            [
                'name' => 'Furniture Bundle',
                'category' => 'Furniture',
                'price' => 999,
                'items' => ['Throne Chair' => 1, 'HC Sofa' => 2, 'Dragons Lamp' => 1],
            ],
            [
                'name' => 'Currency Pack',
                'category' => 'Currency',
                'price' => 799,
                'items' => ['100 Credits' => 3, '50 Duckets' => 2, '25 Diamonds' => 1],
            ],
        ];

        foreach ($packageDefinitions as $def) {
            $package = WebsiteShopPackage::updateOrCreate(
                ['name' => $def['name']],
                [
                    'description' => fake()->sentence(),
                    'price' => $def['price'],
                    'website_shop_category_id' => $categories[$def['category']]->id,
                ],
            );

            $syncData = [];
            foreach ($def['items'] as $itemName => $quantity) {
                $item = $items->first(fn (WebsiteShopItem $i) => $i->name === $itemName);
                if ($item) {
                    $syncData[$item->id] = ['quantity' => $quantity];
                }
            }
            $package->items()->sync($syncData);
        }
    }
}
