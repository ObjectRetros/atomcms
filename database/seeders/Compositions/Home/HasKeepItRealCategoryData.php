<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasKeepItRealCategoryData
{
    public function getKeepItRealItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-keep-it-real-pt-1.png', 'Keep It Real Pt. 1'),
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-keep-it-real-pt-2.png', 'Keep It Real Pt. 2'),
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-keep-it-real-pt-3.png', 'Keep It Real Pt. 3'),
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-keep-it-real-pt-4.png', 'Keep It Real Pt. 4'),
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-keep-it-real-pt-5.png', 'Keep It Real Pt. 5'),
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-button-keep-it-real.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-button-keep-it-real-2.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-button-keep-it-real-3.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-button-keep-it-real-4.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-button-keep-it-real-5.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-keep-it-real.png', 'Keep It Real'),
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-keep-it-real-100-habbo.png', 'Keep It Real 100% Habbo'),
            $this->buildItemStructure($category, '/assets/images/home/items/keep-it-real-100-habbo.png', '100% Habbo'),
        ];
    }
}
