<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasAlhambraCategoryData
{
    public function getAlhambraItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/alhambra-item.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/alhambra-item-2.gif'),
        ];
    }
}
