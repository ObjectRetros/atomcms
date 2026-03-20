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
                'home-items/cat-cine.png',
            ],
            [
                'Bling Alphabet',
                'home-items/cat-bling-alphabet.png',
            ],
            [
                'Keep it Real',
                'home-items/cat-keep-it-real.png',
            ],
            [
                'Summer Vacation',
                'home-items/cat-summer-vacation.gif',
            ],
            [
                'Pirates',
                'home-items/cat-pirates.png',
            ],
            [
                'Plastic Alphabet',
                'home-items/cat-plastic-alphabet.png',
            ],
            [
                'Valentine',
                'home-items/cat-valentine.png',
            ],
            [
                'Wooden Alphabet',
                'home-items/cat-wooden-alphabet.png',
            ],
            [
                'Buttons',
                'home-items/cat-buttons.png',
            ],
            [
                'Alhambra',
                'home-items/cat-alhambra.png',
            ],
            [
                'Sports',
                'home-items/cat-sports.png',
            ],
            [
                'WWE',
                'home-items/cat-wwe.png',
            ],
            [
                'Paintings',
                'home-items/cat-paintings.png',
            ],
            [
                'Dividers',
                'home-items/cat-dividers.png',
            ],
            [
                'SnowStorm',
                'home-items/cat-snowstorm.png',
            ],
            [
                'Habboween',
                'home-items/cat-habboween.png',
            ],
            [
                'Coins and Related',
                'home-items/cat-coins-and-related.png',
            ],
            [
                'Forest and Related',
                'home-items/cat-forest-and-related.png',
            ],
            [
                'Clamps and Related',
                'home-items/cat-clamps-and-related.png',
            ],
        ];
    }
}
