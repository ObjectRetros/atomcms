<?php

namespace Database\Seeders;

use App\Models\Home\HomeCategory;
use App\Models\Home\HomeItem;
use Database\Seeders\Compositions\HasUserProfileData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomeItemSeeder extends Seeder
{
    use HasUserProfileData;

    public int $currentOrder = 1;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (HomeItem::count() > 0) {
            return;
        }

        $this->addItemsForCategory('Cine');
        $this->addItemsForCategory('Bling Alphabet');
        $this->addItemsForCategory('Plastic Alphabet');
        $this->addItemsForCategory('Wooden Alphabet');
        $this->addItemsForCategory('Keep It Real');
        $this->addItemsForCategory('Summer Vacation');
        $this->addItemsForCategory('Pirates');
        $this->addItemsForCategory('Valentine');
        $this->addItemsForCategory('Buttons');
        $this->addItemsForCategory('Alhambra');
        $this->addItemsForCategory('Sports');
        $this->addItemsForCategory('WWE');
        $this->addItemsForCategory('Paintings');
        $this->addItemsForCategory('Dividers');
        $this->addItemsForCategory('SnowStorm');
        $this->addItemsForCategory('Artists');
        $this->addItemsForCategory('Habboween');
        $this->addItemsForCategory('Coins and Related');
        $this->addItemsForCategory('Forest and Related');
        $this->addItemsForCategory('Clamps and Related');

        $this->insertBackgroundsItemsData();
        $this->insertNotesItemsData();
        $this->insertWidgetsItemsData();
    }

    protected function addItemsForCategory(string $categoryName): void
    {
        $this->currentOrder = 1;

        if (! $category = HomeCategory::whereName($categoryName)->first()) {
            return;
        }

        $method = sprintf('get%sItemsData', str_replace(' ', '', $categoryName));

        if (! method_exists($this, $method)) {
            return;
        }

        DB::table('home_items')->insert($this->{$method}($category));
    }
}
