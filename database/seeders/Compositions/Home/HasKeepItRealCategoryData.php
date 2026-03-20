<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasKeepItRealCategoryData
{
    public function getKeepItRealItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/dEzR4N1.png', 'Keep It Real Pt. 1'),
            $this->buildItemStructure($category, 'https://imgur.com/9y0PWl0.png', 'Keep It Real Pt. 2'),
            $this->buildItemStructure($category, 'https://imgur.com/0SEXDju.png', 'Keep It Real Pt. 3'),
            $this->buildItemStructure($category, 'https://imgur.com/a3CippD.png', 'Keep It Real Pt. 4'),
            $this->buildItemStructure($category, 'https://imgur.com/He9Hz2S.png', 'Keep It Real Pt. 5'),
            $this->buildItemStructure($category, 'https://imgur.com/YZp8qWr.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, 'https://imgur.com/WgzxuuC.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, 'https://imgur.com/kOselQy.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, 'https://imgur.com/244fCGo.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, 'https://imgur.com/6wLMxzd.png', 'Button Keep It Real'),
            $this->buildItemStructure($category, 'https://imgur.com/cgdbgk5.png', 'Keep It Real'),
            $this->buildItemStructure($category, 'https://imgur.com/bZR55Mh.png', 'Keep It Real 100% Habbo'),
            $this->buildItemStructure($category, 'https://imgur.com/nnMfsNR.png', '100% Habbo'),
        ];
    }
}
