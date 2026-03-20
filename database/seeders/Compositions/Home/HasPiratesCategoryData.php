<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasPiratesCategoryData
{
    public function getPiratesItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item-2.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item-3.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item-4.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item-5.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item-6.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item-7.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item-8.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item-9.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item-10.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item-11.png'),
        ];
    }
}
