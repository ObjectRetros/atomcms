<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasHabboweenCategoryData
{
    public function getHabboweenItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-2.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-3.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-4.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-5.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-6.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-7.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-8.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-9.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-10.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-11.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-12.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-13.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-14.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-15.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-16.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-17.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-18.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-19.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-20.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-21.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/habboween-item-22.gif'),
        ];
    }
}
