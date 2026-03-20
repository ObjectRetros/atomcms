<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasSummerVacationCategoryData
{
    public function getSummerVacationItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/iABXOA3.png'),
            $this->buildItemStructure($category, 'https://imgur.com/eIuYkXa.png'),
            $this->buildItemStructure($category, 'https://imgur.com/l1LXBmD.png'),
            $this->buildItemStructure($category, 'https://imgur.com/Rrq0Wux.png'),
            $this->buildItemStructure($category, 'https://imgur.com/ptRKwXT.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/1B0ThyU.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/9Dl4ocv.png'),
            $this->buildItemStructure($category, 'https://imgur.com/WSJThij.png'),
            $this->buildItemStructure($category, 'https://imgur.com/SywhBeG.png'),
            $this->buildItemStructure($category, 'https://imgur.com/AHi5CZ0.png'),
            $this->buildItemStructure($category, 'https://imgur.com/9tiqnZy.png'),
            $this->buildItemStructure($category, 'https://imgur.com/TBrTWaf.png'),
            $this->buildItemStructure($category, 'https://imgur.com/yonKkaI.png'),
            $this->buildItemStructure($category, 'https://imgur.com/rSCx8jp.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/OPwdX9y.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/uWmj1Ar.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/3gCH8lC.gif'),
        ];
    }
}
