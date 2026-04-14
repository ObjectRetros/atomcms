<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasValentineCategoryData
{
    public function getValentineItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'home-items/valentine-item.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-2.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-3.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-4.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-5.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-6.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-7.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-8.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-9.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-10.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-11.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-12.gif'),
            $this->buildItemStructure($category, 'home-items/valentine-item-13.gif'),
            $this->buildItemStructure($category, 'home-items/valentine-item-14.gif'),
            $this->buildItemStructure($category, 'home-items/valentine-item-15.gif'),
            $this->buildItemStructure($category, 'home-items/valentine-item-16.gif'),
            $this->buildItemStructure($category, 'home-items/valentine-item-17.gif'),
            $this->buildItemStructure($category, 'home-items/valentine-item-18.gif'),
            $this->buildItemStructure($category, 'home-items/valentine-item-19.gif'),
            $this->buildItemStructure($category, 'home-items/valentine-item-20.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-21.png'),
            $this->buildItemStructure($category, 'home-items/valentine-item-22.gif'),
            $this->buildItemStructure($category, 'home-items/valentine-item-23.gif'),
        ];
    }
}
