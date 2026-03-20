<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasForestAndRelatedCategoryData
{
    public function getForestAndRelatedItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/jK0fI0A.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/iABXOA3.png'),
            $this->buildItemStructure($category, 'https://imgur.com/eIuYkXa.png'),
            $this->buildItemStructure($category, 'https://imgur.com/Rrq0Wux.png'),
            $this->buildItemStructure($category, 'https://imgur.com/TBrTWaf.png'),
            $this->buildItemStructure($category, 'https://imgur.com/16W9AGV.png'),
            $this->buildItemStructure($category, 'https://imgur.com/zst7MoH.png'),
            $this->buildItemStructure($category, 'https://imgur.com/HXuBBzL.png'),
            $this->buildItemStructure($category, 'https://imgur.com/buvpIq2.png'),
            $this->buildItemStructure($category, 'https://imgur.com/wYwMpom.png'),
            $this->buildItemStructure($category, 'https://imgur.com/VGIZYek.png'),
            $this->buildItemStructure($category, 'https://imgur.com/G4yj58h.png'),
            $this->buildItemStructure($category, 'https://imgur.com/FFGPw3E.png'),
            $this->buildItemStructure($category, 'https://imgur.com/u4M0Ue5.png'),
            $this->buildItemStructure($category, 'https://imgur.com/v1mH3Dh.png'),
            $this->buildItemStructure($category, 'https://imgur.com/yWp3vyz.png'),
            $this->buildItemStructure($category, 'https://imgur.com/lZfLeHN.png'),
            $this->buildItemStructure($category, 'https://imgur.com/Jqt6Yd3.png'),
            $this->buildItemStructure($category, 'https://imgur.com/wbkH0VV.png'),
            $this->buildItemStructure($category, 'https://imgur.com/B54Et4F.png'),
            $this->buildItemStructure($category, 'https://imgur.com/0QWbsrz.png'),
            $this->buildItemStructure($category, 'https://imgur.com/l15lfJF.png'),
            $this->buildItemStructure($category, 'https://imgur.com/bhnjUn9.png'),
            $this->buildItemStructure($category, 'https://imgur.com/gETJLQA.png'),
            $this->buildItemStructure($category, 'https://imgur.com/a5EeKPa.png'),
            $this->buildItemStructure($category, 'https://imgur.com/oD9w8wz.png'),
            $this->buildItemStructure($category, 'https://imgur.com/YRNAWqV.png'),
            $this->buildItemStructure($category, 'https://imgur.com/9vOLSzJ.png'),
            $this->buildItemStructure($category, 'https://imgur.com/a83kSx2.png'),
            $this->buildItemStructure($category, 'https://imgur.com/5l1D8yv.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/4G53RPV.png'),
        ];
    }
}
