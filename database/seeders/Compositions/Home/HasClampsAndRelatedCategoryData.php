<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasClampsAndRelatedCategoryData
{
    public function getClampsAndRelatedItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-2.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-3.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-4.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-5.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-6.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-7.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-8.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-9.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-10.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-11.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-12.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-13.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-14.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-15.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-16.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-17.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-18.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-19.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/clamps-and-related-item-20.gif'),
        ];
    }
}
