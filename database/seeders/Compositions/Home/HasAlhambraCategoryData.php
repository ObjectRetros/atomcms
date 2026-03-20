<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasAlhambraCategoryData
{
    public function getAlhambraItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/ggbQ2QG.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/BtN2Ten.gif'),
        ];
    }
}
