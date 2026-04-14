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
        // Furniture - Classic & Rare
        'Throne' => ['type' => 'furniture', 'type_value' => '230'],
        'Throne Sofa' => ['type' => 'furniture', 'type_value' => '287'],
        'Tubmaster' => ['type' => 'furniture', 'type_value' => '288'],
        'Golden Elephant' => ['type' => 'furniture', 'type_value' => '290'],
        'Granite Fountain' => ['type' => 'furniture', 'type_value' => '291'],
        'Habbo Globe' => ['type' => 'furniture', 'type_value' => '293'],
        'Petal Patch' => ['type' => 'furniture', 'type_value' => '285'],
        'Golden Dragon' => ['type' => 'furniture', 'type_value' => '1626'],
        'Frost Dragon' => ['type' => 'furniture', 'type_value' => '1624'],
        'Jade Dragon' => ['type' => 'furniture', 'type_value' => '1622'],
        'Silver Dragon Lamp' => ['type' => 'furniture', 'type_value' => '1628'],
        'Black Dragon Lamp' => ['type' => 'furniture', 'type_value' => '1619'],

        // Furniture - HC Collection
        'Majestic Chair' => ['type' => 'furniture', 'type_value' => '1527'],
        'Nordic Table' => ['type' => 'furniture', 'type_value' => '1528'],
        'Oil Lamp' => ['type' => 'furniture', 'type_value' => '1529'],
        'Study Desk' => ['type' => 'furniture', 'type_value' => '1530'],
        'Persian Carpet' => ['type' => 'furniture', 'type_value' => '2068'],
        'Mega TV Set' => ['type' => 'furniture', 'type_value' => '2069'],
        'Gothic Candelabra' => ['type' => 'furniture', 'type_value' => '2071'],
        'Medieval Bookcase' => ['type' => 'furniture', 'type_value' => '2074'],
        'Electric Butler' => ['type' => 'furniture', 'type_value' => '2075'],
        'Heavy Duty Fireplace' => ['type' => 'furniture', 'type_value' => '2078'],
        'Victorian Street Light' => ['type' => 'furniture', 'type_value' => '2079'],
        'Weird Science Machine' => ['type' => 'furniture', 'type_value' => '2080'],
        'Drinks Trolley' => ['type' => 'furniture', 'type_value' => '2083'],

        // Furniture - Gothic Set
        'Gothic Chair Pink' => ['type' => 'furniture', 'type_value' => '2084'],
        'Gothic Sofa Pink' => ['type' => 'furniture', 'type_value' => '2085'],
        'Gothic Chair Black' => ['type' => 'furniture', 'type_value' => '2093'],
        'Gothic Sofa Black' => ['type' => 'furniture', 'type_value' => '2094'],
        'Cobbled Path' => ['type' => 'furniture', 'type_value' => '2133'],

        // Furniture - Executive Set
        'Executive Carpet' => ['type' => 'furniture', 'type_value' => '2567'],
        'Glass Table' => ['type' => 'furniture', 'type_value' => '2581'],
        'Three Seat Sofa' => ['type' => 'furniture', 'type_value' => '2639'],
        'Boss Chair' => ['type' => 'furniture', 'type_value' => '2650'],
        'Executive Globe' => ['type' => 'furniture', 'type_value' => '2672'],

        // Furniture - Diner Set
        'Diner Rug' => ['type' => 'furniture', 'type_value' => '2803'],
        'Plate With Hamburger' => ['type' => 'furniture', 'type_value' => '2839'],
        'Plate With Pancakes' => ['type' => 'furniture', 'type_value' => '2800'],
        'Diner Shaker' => ['type' => 'furniture', 'type_value' => '2814'],
        'Ketchup and Mustard' => ['type' => 'furniture', 'type_value' => '2840'],

        // Furniture - Misc
        'Cupid Statue' => ['type' => 'furniture', 'type_value' => '226'],
        'Giant Heart' => ['type' => 'furniture', 'type_value' => '227'],
        'Heart Sofa' => ['type' => 'furniture', 'type_value' => '229'],
        'Bubble Bath' => ['type' => 'furniture', 'type_value' => '174'],
        'DJ Turntable' => ['type' => 'furniture', 'type_value' => '449'],
        'Digital TV' => ['type' => 'furniture', 'type_value' => '173'],
        'Mystery Box' => ['type' => 'furniture', 'type_value' => '4692'],
        'Holoboy' => ['type' => 'furniture', 'type_value' => '234'],
        'Holodice' => ['type' => 'furniture', 'type_value' => '239'],
        'Bonsai Tree' => ['type' => 'furniture', 'type_value' => '163'],
        'Cherry Tree' => ['type' => 'furniture', 'type_value' => '161'],
        'Pineapple Plant' => ['type' => 'furniture', 'type_value' => '160'],
        'Gold Trophy' => ['type' => 'furniture', 'type_value' => '185'],
        'Silver Trophy' => ['type' => 'furniture', 'type_value' => '186'],

        // Currency
        '500 Credits' => ['type' => 'currency', 'type_value' => 'credits:500'],
        '1000 Credits' => ['type' => 'currency', 'type_value' => 'credits:1000'],
        '5000 Credits' => ['type' => 'currency', 'type_value' => 'credits:5000'],
        '100 Duckets' => ['type' => 'currency', 'type_value' => 'duckets:100'],
        '500 Duckets' => ['type' => 'currency', 'type_value' => 'duckets:500'],
        '50 Diamonds' => ['type' => 'currency', 'type_value' => 'diamonds:50'],
        '100 Diamonds' => ['type' => 'currency', 'type_value' => 'diamonds:100'],
        '500 Diamonds' => ['type' => 'currency', 'type_value' => 'diamonds:500'],

        // Badges
        'VIP Badge' => ['type' => 'badge', 'type_value' => 'VIP01'],
        'Staff Badge' => ['type' => 'badge', 'type_value' => 'ADM'],
        'Beta Tester Badge' => ['type' => 'badge', 'type_value' => 'BTA'],

        // Ranks
        'VIP Rank' => ['type' => 'rank', 'type_value' => '2'],
    ];

    private array $categories = [
        'Starter Packs',
        'Currency',
        'VIP',
        'Rare Collections',
        'Room Sets',
        'Seasonal',
        'Limited Edition',
    ];

    public function run(): void
    {
        $categories = collect($this->categories)->mapWithKeys(function (string $name) {
            $category = WebsiteShopCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true],
            );

            return [$name => $category];
        });

        $items = collect($this->items)->mapWithKeys(function (array $data, string $name) {
            $item = WebsiteShopItem::updateOrCreate(
                ['name' => $name],
                [...$data, 'is_active' => true],
            );

            return [$name => $item];
        });

        $packageDefinitions = [
            // Starter Packs
            [
                'name' => 'Welcome Pack',
                'description' => 'Everything you need to get started! Includes credits, a bonsai tree, and a digital TV for your first room.',
                'category' => 'Starter Packs',
                'price' => 299,
                'items' => ['500 Credits' => 1, 'Bonsai Tree' => 1, 'Digital TV' => 1],
            ],
            [
                'name' => 'New Player Bundle',
                'description' => 'A great starter bundle with currency, plants, and essential furniture.',
                'category' => 'Starter Packs',
                'price' => 499,
                'items' => ['1000 Credits' => 1, '100 Duckets' => 1, 'Cherry Tree' => 1, 'Pineapple Plant' => 1, 'Bubble Bath' => 1],
            ],
            [
                'name' => 'Room Starter Kit',
                'description' => 'Furnish your room in style with this essential furniture starter kit.',
                'category' => 'Starter Packs',
                'price' => 699,
                'items' => ['500 Credits' => 1, '100 Duckets' => 1, 'DJ Turntable' => 1, 'Holodice' => 1, 'Bonsai Tree' => 2],
            ],

            // Currency Packs
            [
                'name' => 'Credits Boost',
                'description' => 'A quick credits boost to keep you shopping.',
                'category' => 'Currency',
                'price' => 499,
                'items' => ['1000 Credits' => 1],
            ],
            [
                'name' => 'Credits Mega Pack',
                'description' => 'Massive credits injection for the serious collector.',
                'category' => 'Currency',
                'price' => 1499,
                'items' => ['5000 Credits' => 1],
            ],
            [
                'name' => 'Diamond Stash',
                'description' => 'Stock up on diamonds for rare trades.',
                'category' => 'Currency',
                'price' => 999,
                'items' => ['100 Diamonds' => 1],
            ],
            [
                'name' => 'Diamond Vault',
                'description' => 'The ultimate diamond package for high-value trades.',
                'category' => 'Currency',
                'price' => 3999,
                'items' => ['500 Diamonds' => 1],
            ],
            [
                'name' => 'Mixed Currency Bundle',
                'description' => 'A balanced mix of all currencies.',
                'category' => 'Currency',
                'price' => 1299,
                'items' => ['1000 Credits' => 1, '500 Duckets' => 1, '50 Diamonds' => 1],
            ],
            [
                'name' => 'Duckets Pack',
                'description' => 'Need duckets for room effects? This is your pack.',
                'category' => 'Currency',
                'price' => 599,
                'items' => ['500 Duckets' => 1],
            ],

            // VIP
            [
                'name' => 'VIP Membership',
                'description' => 'Unlock VIP status with exclusive badge, rank, and a generous currency bonus.',
                'category' => 'VIP',
                'price' => 1999,
                'is_giftable' => false,
                'items' => ['VIP Rank' => 1, 'VIP Badge' => 1, '5000 Credits' => 1, '100 Diamonds' => 1],
            ],
            [
                'name' => 'VIP Furniture Bundle',
                'description' => 'Exclusive HC furniture only available to VIP members.',
                'category' => 'VIP',
                'price' => 2499,
                'min_rank' => 2,
                'items' => ['Majestic Chair' => 2, 'Nordic Table' => 1, 'Persian Carpet' => 1, 'Heavy Duty Fireplace' => 1, 'Victorian Street Light' => 2, 'Electric Butler' => 1],
            ],
            [
                'name' => 'VIP Deluxe',
                'description' => 'The complete VIP experience: rank, badge, currency, and premium furniture.',
                'category' => 'VIP',
                'price' => 4999,
                'is_giftable' => false,
                'items' => ['VIP Rank' => 1, 'VIP Badge' => 1, '5000 Credits' => 1, '500 Diamonds' => 1, '500 Duckets' => 1, 'Throne' => 1, 'Throne Sofa' => 2, 'Mega TV Set' => 1],
            ],

            // Rare Collections
            [
                'name' => 'Dragon Collection',
                'description' => 'Collect all the legendary dragon lamps! Five unique dragons in one package.',
                'category' => 'Rare Collections',
                'price' => 2999,
                'stock' => 50,
                'items' => ['Golden Dragon' => 1, 'Frost Dragon' => 1, 'Jade Dragon' => 1, 'Silver Dragon Lamp' => 1, 'Black Dragon Lamp' => 1],
            ],
            [
                'name' => 'Rare Elephant Set',
                'description' => 'The iconic golden elephant statue with a matching fountain.',
                'category' => 'Rare Collections',
                'price' => 1499,
                'stock' => 100,
                'items' => ['Golden Elephant' => 1, 'Granite Fountain' => 1, 'Petal Patch' => 2],
            ],
            [
                'name' => 'Trophy Room',
                'description' => 'Show off your achievements with gold and silver trophies.',
                'category' => 'Rare Collections',
                'price' => 799,
                'items' => ['Gold Trophy' => 2, 'Silver Trophy' => 3, 'Holoboy' => 1],
            ],
            [
                'name' => 'Collectors Mystery Bundle',
                'description' => 'A surprise selection of rare items for true collectors. Limited availability!',
                'category' => 'Rare Collections',
                'price' => 3499,
                'stock' => 25,
                'limit_per_user' => 1,
                'items' => ['Habbo Globe' => 1, 'Weird Science Machine' => 1, 'Gothic Candelabra' => 2, 'Mystery Box' => 3],
            ],

            // Room Sets
            [
                'name' => 'Gothic Room Set',
                'description' => 'Transform your room with the complete dark gothic furniture collection.',
                'category' => 'Room Sets',
                'price' => 1799,
                'items' => ['Gothic Chair Black' => 2, 'Gothic Sofa Black' => 2, 'Gothic Chair Pink' => 2, 'Gothic Sofa Pink' => 1, 'Cobbled Path' => 3, 'Gothic Candelabra' => 2],
            ],
            [
                'name' => 'Executive Office',
                'description' => 'A premium office setup for the business-minded Habbo.',
                'category' => 'Room Sets',
                'price' => 1299,
                'items' => ['Executive Carpet' => 2, 'Glass Table' => 1, 'Three Seat Sofa' => 1, 'Boss Chair' => 1, 'Executive Globe' => 1],
            ],
            [
                'name' => 'Retro Diner',
                'description' => 'Build the perfect diner with booths, food, and all the trimmings.',
                'category' => 'Room Sets',
                'price' => 999,
                'items' => ['Diner Rug' => 2, 'Plate With Hamburger' => 2, 'Plate With Pancakes' => 2, 'Diner Shaker' => 1, 'Ketchup and Mustard' => 2],
            ],
            [
                'name' => 'HC Study Room',
                'description' => 'A scholarly room featuring rare HC furniture pieces.',
                'category' => 'Room Sets',
                'price' => 1599,
                'items' => ['Study Desk' => 1, 'Oil Lamp' => 2, 'Medieval Bookcase' => 2, 'Majestic Chair' => 1, 'Drinks Trolley' => 1],
            ],

            // Seasonal
            [
                'name' => 'Valentine\'s Romance',
                'description' => 'Spread the love with this romantic Valentine\'s themed package.',
                'category' => 'Seasonal',
                'price' => 899,
                'available_from' => '2026-02-01',
                'available_to' => '2026-02-28',
                'items' => ['Cupid Statue' => 1, 'Giant Heart' => 2, 'Heart Sofa' => 2],
            ],
            [
                'name' => 'Royal Throne Room',
                'description' => 'Rule your hotel room with a throne and golden decorations.',
                'category' => 'Seasonal',
                'price' => 2499,
                'items' => ['Throne' => 1, 'Throne Sofa' => 2, 'Tubmaster' => 1, 'Victorian Street Light' => 2, 'Persian Carpet' => 2],
            ],

            // Limited Edition
            [
                'name' => 'Founder\'s Package',
                'description' => 'An exclusive package for early supporters. Includes rare items, VIP status, and a massive currency boost. Once they\'re gone, they\'re gone!',
                'category' => 'Limited Edition',
                'price' => 9999,
                'stock' => 10,
                'limit_per_user' => 1,
                'is_giftable' => false,
                'items' => ['VIP Rank' => 1, 'VIP Badge' => 1, 'Beta Tester Badge' => 1, '5000 Credits' => 2, '500 Diamonds' => 1, 'Golden Dragon' => 1, 'Throne' => 1, 'Golden Elephant' => 1],
            ],
            [
                'name' => 'Ultra Rare Dragon Bundle',
                'description' => 'The complete dragon lamp collection with bonus diamonds. Extremely limited!',
                'category' => 'Limited Edition',
                'price' => 4999,
                'stock' => 20,
                'limit_per_user' => 2,
                'items' => ['Golden Dragon' => 2, 'Frost Dragon' => 2, 'Jade Dragon' => 2, 'Silver Dragon Lamp' => 2, 'Black Dragon Lamp' => 2, '100 Diamonds' => 1],
            ],
        ];

        foreach ($packageDefinitions as $sortOrder => $def) {
            $package = WebsiteShopPackage::updateOrCreate(
                ['name' => $def['name']],
                [
                    'description' => $def['description'],
                    'price' => $def['price'],
                    'website_shop_category_id' => $categories[$def['category']]->id,
                    'sort_order' => $sortOrder,
                    'is_giftable' => $def['is_giftable'] ?? true,
                    'min_rank' => $def['min_rank'] ?? null,
                    'max_rank' => $def['max_rank'] ?? null,
                    'stock' => $def['stock'] ?? null,
                    'limit_per_user' => $def['limit_per_user'] ?? null,
                    'available_from' => $def['available_from'] ?? null,
                    'available_to' => $def['available_to'] ?? null,
                ],
            );

            $syncData = [];
            foreach ($def['items'] as $itemName => $quantity) {
                if ($items->has($itemName)) {
                    $syncData[$items[$itemName]->id] = ['quantity' => $quantity];
                }
            }
            $package->items()->sync($syncData);
        }
    }
}
