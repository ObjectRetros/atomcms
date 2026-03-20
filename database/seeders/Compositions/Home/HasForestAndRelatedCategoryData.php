<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasForestAndRelatedCategoryData
{
    public function getForestAndRelatedItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/jK0fI0A.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/iABXOA3.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/eIuYkXa.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/Rrq0Wux.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/TBrTWaf.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/16W9AGV.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/zst7MoH.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/HXuBBzL.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/buvpIq2.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/wYwMpom.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/VGIZYek.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/G4yj58h.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/FFGPw3E.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/u4M0Ue5.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/v1mH3Dh.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/yWp3vyz.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/lZfLeHN.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/Jqt6Yd3.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/wbkH0VV.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/B54Et4F.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/0QWbsrz.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/l15lfJF.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/bhnjUn9.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/gETJLQA.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/a5EeKPa.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/oD9w8wz.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/YRNAWqV.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/9vOLSzJ.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/a83kSx2.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/5l1D8yv.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/4G53RPV.png'),
        ];
    }
}
