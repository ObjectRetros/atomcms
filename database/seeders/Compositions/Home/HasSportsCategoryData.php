<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasSportsCategoryData
{
    public function getSportsItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'home-items/summer-vacation-item-5.gif'),
            $this->buildItemStructure($category, 'home-items/summer-vacation-item-14.gif'),
            $this->buildItemStructure($category, 'home-items/sports-item-3.gif'),
            $this->buildItemStructure($category, 'home-items/sports-item-4.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-5.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-6.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-7.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-8.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-9.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-10.gif'),
            $this->buildItemStructure($category, 'home-items/sports-item-11.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-12.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-13.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-14.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-15.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-16.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-17.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-18.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-19.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-20.gif'),
            $this->buildItemStructure($category, 'home-items/sports-item-21.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-22.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-23.png'),
            $this->buildItemStructure($category, 'home-items/sports-item-24.gif'),
        ];
    }
}
