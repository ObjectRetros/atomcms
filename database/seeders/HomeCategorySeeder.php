<?php

namespace Database\Seeders;

use App\Models\Home\HomeCategory;
use Illuminate\Database\Seeder;

class HomeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order = 1;

        foreach ($this->getDefaultHomeCategories() as $category) {
            HomeCategory::firstOrCreate(['name' => $category[0]], [
                'icon' => $category[1],
                'order' => $order++,
            ]);
        }
    }

    public function getDefaultHomeCategories(): array
    {
        return [
            [
                'Cine',
                '/assets/images/home/items/cat-cine.png',
            ],
            [
                'Bling Alphabet',
                '/assets/images/home/items/cat-bling-alphabet.png',
            ],
            [
                'Keep it Real',
                '/assets/images/home/items/cat-keep-it-real.png',
            ],
            [
                'Summer Vacation',
                '/assets/images/home/items/cat-summer-vacation.gif',
            ],
            [
                'Pirates',
                '/assets/images/home/items/cat-pirates.png',
            ],
            [
                'Plastic Alphabet',
                '/assets/images/home/items/cat-plastic-alphabet.png',
            ],
            [
                'Valentine',
                '/assets/images/home/items/cat-valentine.png',
            ],
            [
                'Wooden Alphabet',
                '/assets/images/home/items/cat-wooden-alphabet.png',
            ],
            [
                'Buttons',
                '/assets/images/home/items/cat-buttons.png',
            ],
            [
                'Alhambra',
                '/assets/images/home/items/cat-alhambra.png',
            ],
            [
                'Sports',
                '/assets/images/home/items/cat-sports.png',
            ],
            [
                'WWE',
                '/assets/images/home/items/cat-wwe.png',
            ],
            [
                'Paintings',
                '/assets/images/home/items/cat-paintings.png',
            ],
            [
                'Dividers',
                '/assets/images/home/items/cat-dividers.png',
            ],
            [
                'SnowStorm',
                '/assets/images/home/items/cat-snowstorm.png',
            ],
            [
                'Habboween',
                '/assets/images/home/items/cat-habboween.png',
            ],
            [
                'Coins and Related',
                '/assets/images/home/items/cat-coins-and-related.png',
            ],
            [
                'Forest and Related',
                '/assets/images/home/items/cat-forest-and-related.png',
            ],
            [
                'Clamps and Related',
                '/assets/images/home/items/cat-clamps-and-related.png',
            ],
        ];
    }
}
