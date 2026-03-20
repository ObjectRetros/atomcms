<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasCoinsAndRelatedCategoryData
{
    public function getCoinsAndRelatedItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/cine-item-28.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/cine-item-9.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/cine-item-11.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/cine-item-10.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item-10.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/pirates-item-11.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/coins-and-related-item-7.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/coins-and-related-item-8.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/coins-and-related-item-9.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/coins-and-related-item-10.png'),
        ];
    }
}
