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
                'https://i.imgur.com/DH45rww.png',
            ],
            [
                'Bling Alphabet',
                'https://i.imgur.com/miq0Aiv.png',
            ],
            [
                'Keep it Real',
                'https://i.imgur.com/pmSUjjP.png',
            ],
            [
                'Summer Vacation',
                'https://i.imgur.com/abzSMEH.gif',
            ],
            [
                'Pirates',
                'https://i.imgur.com/XTRzzsI.png',
            ],
            [
                'Plastic Alphabet',
                'https://i.imgur.com/A3VBOq9.png',
            ],
            [
                'Valentine',
                'https://i.imgur.com/K0HqFx4.png',
            ],
            [
                'Wooden Alphabet',
                'https://i.imgur.com/ziDIYgy.png',
            ],
            [
                'Buttons',
                'https://i.imgur.com/lzfYaYp.png',
            ],
            [
                'Alhambra',
                'https://i.imgur.com/Jry4aC6.png',
            ],
            [
                'Sports',
                'https://i.imgur.com/BDCtism.png',
            ],
            [
                'WWE',
                'https://i.imgur.com/ML7YRub.png',
            ],
            [
                'Paintings',
                'https://i.imgur.com/UCvX3St.png',
            ],
            [
                'Dividers',
                'https://i.imgur.com/vgjnpff.png',
            ],
            [
                'SnowStorm',
                'https://i.imgur.com/oevdfAb.png',
            ],
            [
                'Habboween',
                'https://i.imgur.com/NibQAwu.png',
            ],
            [
                'Coins and Related',
                'https://imgur.com/2dv241o.png',
            ],
            [
                'Forest and Related',
                'https://imgur.com/93S5hn6.png',
            ],
            [
                'Clamps and Related',
                'https://imgur.com/9cAAtv0.png',
            ],
        ];
    }
}
