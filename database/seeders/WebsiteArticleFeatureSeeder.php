<?php

namespace Database\Seeders;

use App\Models\Shop\WebsiteShopArticle;
use App\Models\Shop\WebsiteShopArticleFeature;
use Illuminate\Database\Seeder;

class WebsiteArticleFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $articles = WebsiteShopArticle::all();
        $firstArticle = $articles[0];
        $secondArticle = $articles[1];
        $thirdArticle = $articles[2];

        if (! $firstArticle || ! $secondArticle || ! $thirdArticle) {
            return;
        }

        $features = [
            [
                'article_id' => $firstArticle->id,
                'content' => 'Bronze VIP badge',
            ],
            [
                'article_id' => $firstArticle->id,
                'content' => 'Bronze VIP catalogue',
            ],
            [
                'article_id' => $firstArticle->id,
                'content' => 'Bronze VIP commands',
            ],
            [
                'article_id' => $firstArticle->id,
                'content' => '350 credits every 15 minutes',
            ],
            [
                'article_id' => $firstArticle->id,
                'content' => '350 duckets every 15 minutes',
            ],
            [
                'article_id' => $firstArticle->id,
                'content' => '1 diamond every hour',
            ],

            // Silver VIP
            [
                'article_id' => $secondArticle->id,
                'content' => 'Silver VIP badge',
            ],
            [
                'article_id' => $secondArticle->id,
                'content' => 'Silver VIP catalogue',
            ],
            [
                'article_id' => $secondArticle->id,
                'content' => 'Silver VIP commands',
            ],
            [
                'article_id' => $secondArticle->id,
                'content' => '450 credits every 15 minutes',
            ],
            [
                'article_id' => $secondArticle->id,
                'content' => '450 duckets every 15 minutes',
            ],
            [
                'article_id' => $secondArticle->id,
                'content' => '3 diamond every hour',
            ],

            // Gold VIP
            [
                'article_id' => $thirdArticle->id,
                'content' => 'Gold VIP badge',
            ],
            [
                'article_id' => $thirdArticle->id,
                'content' => 'Gold VIP catalogue',
            ],
            [
                'article_id' => $thirdArticle->id,
                'content' => 'Gold VIP commands',
            ],
            [
                'article_id' => $thirdArticle->id,
                'content' => '550 credits every 15 minutes',
            ],
            [
                'article_id' => $thirdArticle->id,
                'content' => '550 duckets every 15 minutes',
            ],
            [
                'article_id' => $thirdArticle->id,
                'content' => '5 diamond every hour',
            ],
        ];

        WebsiteShopArticleFeature::insert($features, ['article_id']);
    }
}
