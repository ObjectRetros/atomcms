<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasButtonsCategoryData
{
    public function getButtonsItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'home-items/buttons-item.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-2.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-3.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-4.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-5.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-6.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-7.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-8.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-9.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-10.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-11.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-12.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-13.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-14.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-15.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-16.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-17.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-18.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-19.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-20.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-21.png'),
            $this->buildItemStructure($category, 'home-items/buttons-item-22.png'),
        ];
    }
}
