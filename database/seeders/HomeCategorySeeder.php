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
                '/assets/images/home/items/DH45rww.png',
            ],
            [
                'Bling Alphabet',
                '/assets/images/home/items/miq0Aiv.png',
            ],
            [
                'Keep it Real',
                '/assets/images/home/items/pmSUjjP.png',
            ],
            [
                'Summer Vacation',
                '/assets/images/home/items/abzSMEH.gif',
            ],
            [
                'Pirates',
                '/assets/images/home/items/XTRzzsI.png',
            ],
            [
                'Plastic Alphabet',
                '/assets/images/home/items/A3VBOq9.png',
            ],
            [
                'Valentine',
                '/assets/images/home/items/K0HqFx4.png',
            ],
            [
                'Wooden Alphabet',
                '/assets/images/home/items/ziDIYgy.png',
            ],
            [
                'Buttons',
                '/assets/images/home/items/lzfYaYp.png',
            ],
            [
                'Alhambra',
                '/assets/images/home/items/Jry4aC6.png',
            ],
            [
                'Sports',
                '/assets/images/home/items/BDCtism.png',
            ],
            [
                'WWE',
                '/assets/images/home/items/ML7YRub.png',
            ],
            [
                'Paintings',
                '/assets/images/home/items/UCvX3St.png',
            ],
            [
                'Dividers',
                '/assets/images/home/items/vgjnpff.png',
            ],
            [
                'SnowStorm',
                '/assets/images/home/items/oevdfAb.png',
            ],
            [
                'Habboween',
                '/assets/images/home/items/NibQAwu.png',
            ],
            [
                'Coins and Related',
                '/assets/images/home/items/2dv241o.png',
            ],
            [
                'Forest and Related',
                '/assets/images/home/items/93S5hn6.png',
            ],
            [
                'Clamps and Related',
                '/assets/images/home/items/9cAAtv0.png',
            ],
        ];
    }
}
