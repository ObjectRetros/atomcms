<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasSnowStormCategoryData
{
    public function getSnowStormItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'home-items/snowstorm-item.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-2.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-3.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-4.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-5.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-6.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-7.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-8.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-9.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-10.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-11.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-12.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-13.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-14.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-15.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-16.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-17.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-18.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-19.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-20.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-21.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-22.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-23.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-24.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-25.png'),
            $this->buildItemStructure($category, 'home-items/snowstorm-item-26.png'),
        ];
    }
}
