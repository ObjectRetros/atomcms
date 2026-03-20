<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasValentineCategoryData
{
    public function getValentineItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/1BeAsIB.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/z3TLtBL.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/16W9AGV.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/zst7MoH.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/HXuBBzL.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/gpG2STS.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/NwgTomA.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/a49rzmg.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/RAr4w9C.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/7IKujwt.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/fRsrrSv.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/5Q53hhN.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/HQDKpXM.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/AnNtUzs.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/nHmT3W2.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/SfMEDi8.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/fuiweKX.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/JLoB2Tm.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/e6zqF7Z.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/83EMF2X.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/CYeRvj5.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/aIRHUMO.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/5GTmVFY.gif'),
        ];
    }
}
