<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasPaintingsCategoryData
{
    public function getPaintingsItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-2.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-3.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-4.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-5.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-6.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-7.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-8.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-9.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-10.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-11.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-12.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-13.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-14.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-15.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-16.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-17.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-18.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-19.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-20.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-21.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-22.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-23.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-24.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/paintings-item-25.png'),
        ];
    }
}
