<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasKeepItRealCategoryData
{
    public function getKeepItRealItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/dEzR4N1.png', 'Keep It Real Pt. 1'),
            $this->buildItemStructure($category, '/assets/images/home/items/9y0PWl0.png', 'Keep It Real Pt. 2'),
            $this->buildItemStructure($category, '/assets/images/home/items/0SEXDju.png', 'Keep It Real Pt. 3'),
            $this->buildItemStructure($category, '/assets/images/home/items/a3CippD.png', 'Keep It Real Pt. 4'),
            $this->buildItemStructure($category, '/assets/images/home/items/He9Hz2S.png', 'Keep It Real Pt. 5'),
            $this->buildItemStructure($category, '/assets/images/home/items/YZp8qWr.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, '/assets/images/home/items/WgzxuuC.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, '/assets/images/home/items/kOselQy.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, '/assets/images/home/items/244fCGo.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, '/assets/images/home/items/6wLMxzd.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, '/assets/images/home/items/cgdbgk5.png', 'Keep It Real'),
            $this->buildItemStructure($category, '/assets/images/home/items/bZR55Mh.png', 'Keep It Real 100% Habbo'),
            $this->buildItemStructure($category, '/assets/images/home/items/nnMfsNR.png', '100% Habbo'),
        ];
    }
}
