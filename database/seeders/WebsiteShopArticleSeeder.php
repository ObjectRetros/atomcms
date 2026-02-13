<?php

namespace Database\Seeders;

use App\Models\Shop\WebsiteShopArticle;
use App\Models\Shop\WebsiteShopCategory;
use Illuminate\Database\Seeder;

class WebsiteShopArticleSeeder extends Seeder
{
    public function run(): void
    {
        $vipCategory = WebsiteShopCategory::where('name', 'VIP')->firstOrCreate(['slug' => 'vip'], [
            'name' => 'VIP',
            'icon_url' => 'https://i.imgur.com/5NBdR0z.gif',
        ]);

        $articles = [
            [
                'website_shop_category_id' => $vipCategory->id,
                'name' => 'Bronze Package',
                'info' => 'Embark on your Habbo adventure with the Bronze Package – your essential starter kit for the virtual world. Start building your dream rooms, customize your avatar, and get access to basic perks that will make your stay more enjoyable.',
                'icon_url' => asset('assets/images/shop/vip-bronze.gif'),
                'color' => '#c5630f',
                'costs' => 500,
                'give_rank' => 2,
                'credits' => 5000,
                'duckets' => 5000,
                'diamonds' => 100,
                'badges' => 'VipParties2',
                'furniture' => null,
            ],
            [
                'website_shop_category_id' => $vipCategory->id,
                'name' => 'Silver Package',
                'info' => 'The Silver Package is designed for Habbos who are ready to take their experience up a notch. Enjoy a substantial boost of in-game currency and exclusive access to items that will set you apart from the crowd.',
                'icon_url' => asset('assets/images/shop/vip-silver.gif'),
                'color' => '#dddddd',
                'costs' => 1000,
                'give_rank' => 3,
                'credits' => 10000,
                'duckets' => 10000,
                'diamonds' => 300,
                'badges' => 'VipParties2_Top100',
                'furniture' => null,
            ],
            [
                'website_shop_category_id' => $vipCategory->id,
                'name' => 'Gold VIP',
                'info' => 'The Gold VIP package is the epitome of Habbo luxury, reserved for those who crave the ultimate in virtual prestige. Enjoy unparalleled access to exclusive items, premium features, and a status symbol that will make heads turn wherever you go in the Hotel.',
                'icon_url' => asset('assets/images/shop/vip-gold.gif'),
                'color' => '#E4A317FF',
                'costs' => 1500,
                'give_rank' => 4,
                'credits' => 15000,
                'duckets' => 15000,
                'diamonds' => 500,
                'badges' => 'VipParties2_Top10',
                'furniture' => null,
            ],
            // Example for furniture pack
            /*
            [
                'name' => 'Furniture Pack #1',
                'info' => 'The perfect pack for the casino builder',
                'icon' => 'gold',
                'color' => '#E4A317FF',
                'costs' => 750,
                'badges' => 'VipParties2_Top100',
                'furniture' => json_encode([
                    [
                        'item_id' => 202,
                        'amount' => 5,
                    ],
                    [
                        'item_id' => 212,
                        'amount' => 2,
                    ],
                    [
                        'item_id' => 230,
                        'amount' => 2,
                    ],
                ]),
            ],
            */
            // Example for badge pack
            /*
            [
                'name' => 'Badge Pack #1',
                'info' => 'The perfect pack for the badge collector',
                'icon' => 'gold',
                'color' => '#E4A317FF',
                'costs' => 250,
                'badges' => 'BADGE_CODE_1;BADGE_CODE_2;BADGE_CODE_3;BADGE_CODE_4;BADGE_CODE_5',
            ],
            */

            // Example for currency pack
            /*
            [
                'name' => 'Currency Pack #1',
                'info' => 'The perfect pack to boost your account',
                'icon' => 'gold',
                'color' => '#E4A317FF',
                'costs' => 250,
                'credits' => 25000,
                'duckets' => 35000,
                'diamonds' => 2500,
            ],
            */
        ];

        foreach ($articles as $article) {
            WebsiteShopArticle::updateOrCreate(['name' => $article['name']], [
                'website_shop_category_id' => $vipCategory->id,
                'name' => $article['name'],
                'info' => $article['info'],
                'icon_url' => $article['icon_url'],
                'color' => $article['color'],
                'costs' => $article['costs'],
                'give_rank' => $article['give_rank'] ?? null,
                'credits' => $article['credits'] ?? null,
                'duckets' => $article['duckets'] ?? null,
                'diamonds' => $article['diamonds'] ?? null,
                'badges' => $article['badges'] ?? null,
                'furniture' => $article['furniture'] ?? null,
            ]);
        }
    }
}
