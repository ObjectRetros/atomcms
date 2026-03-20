<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasSummerVacationCategoryData
{
    public function getSummerVacationItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-2.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-3.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-4.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-5.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-6.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-7.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-8.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-9.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-10.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-11.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-12.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-13.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-14.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-15.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-16.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/summer-vacation-item-17.gif'),
        ];
    }
}
