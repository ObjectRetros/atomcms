<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasForestAndRelatedCategoryData
{
    public function getForestAndRelatedItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'home-items/cine-item-39.gif'),
            $this->buildItemStructure($category, 'home-items/summer-vacation-item.png'),
            $this->buildItemStructure($category, 'home-items/summer-vacation-item-2.png'),
            $this->buildItemStructure($category, 'home-items/summer-vacation-item-4.png'),
            $this->buildItemStructure($category, 'home-items/summer-vacation-item-12.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-3.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-4.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-5.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-9.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-10.png'),
            $this->buildItemStructure($category, 'home-items/dividers-item-27.png'),
            $this->buildItemStructure($category, 'home-items/dividers-item-28.png'),
            $this->buildItemStructure($category, 'home-items/dividers-item-31.png'),
            $this->buildItemStructure($category, 'home-items/dividers-item-32.png'),
            $this->buildItemStructure($category, 'home-items/dividers-item-39.png'),
            $this->buildItemStructure($category, 'home-items/dividers-item-40.png'),
            $this->buildItemStructure($category, 'home-items/dividers-item-41.png'),
            $this->buildItemStructure($category, 'home-items/dividers-item-42.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-19.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-20.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-21.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-22.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-23.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-24.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-25.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-26.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-27.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-28.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-29.png'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-30.gif'),
            $this->buildItemStructure($category, 'home-items/forest-and-related-item-31.png'),
        ];
    }
}
