<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasSummerVacationCategoryData
{
    public function getSummerVacationItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/iABXOA3.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/eIuYkXa.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/l1LXBmD.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/Rrq0Wux.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/ptRKwXT.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/1B0ThyU.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/9Dl4ocv.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/WSJThij.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/SywhBeG.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/AHi5CZ0.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/9tiqnZy.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/TBrTWaf.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/yonKkaI.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/rSCx8jp.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/OPwdX9y.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/uWmj1Ar.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/3gCH8lC.gif'),
        ];
    }
}
