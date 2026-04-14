<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasCineCategoryData
{
    public function getCineItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'home-items/cine-item.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-2.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-3.gif'),
            $this->buildItemStructure($category, 'home-items/cine-item-4.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-5.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-6.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-7.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-8.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-9.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-10.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-11.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-12.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-13.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-14.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-15.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-16.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-17.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-18.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-19.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-20.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-21.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-22.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-23.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-24.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-25.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-26.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-27.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-28.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-29.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-30.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-31.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-32.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-33.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-34.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-35.png'),
            $this->buildItemStructure($category, 'home-items/cine-item-36.gif'),
            $this->buildItemStructure($category, 'home-items/cine-item-37.gif'),
            $this->buildItemStructure($category, 'home-items/cine-item-38.gif'),
            $this->buildItemStructure($category, 'home-items/cine-item-39.gif'),
        ];
    }
}
