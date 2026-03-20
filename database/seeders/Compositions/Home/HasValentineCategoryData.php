<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasValentineCategoryData
{
    public function getValentineItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/1BeAsIB.png'),
            $this->buildItemStructure($category, 'https://imgur.com/z3TLtBL.png'),
            $this->buildItemStructure($category, 'https://imgur.com/16W9AGV.png'),
            $this->buildItemStructure($category, 'https://imgur.com/zst7MoH.png'),
            $this->buildItemStructure($category, 'https://imgur.com/HXuBBzL.png'),
            $this->buildItemStructure($category, 'https://imgur.com/gpG2STS.png'),
            $this->buildItemStructure($category, 'https://imgur.com/NwgTomA.png'),
            $this->buildItemStructure($category, 'https://imgur.com/a49rzmg.png'),
            $this->buildItemStructure($category, 'https://imgur.com/RAr4w9C.png'),
            $this->buildItemStructure($category, 'https://imgur.com/7IKujwt.png'),
            $this->buildItemStructure($category, 'https://imgur.com/fRsrrSv.png'),
            $this->buildItemStructure($category, 'https://imgur.com/5Q53hhN.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/HQDKpXM.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/AnNtUzs.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/nHmT3W2.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/SfMEDi8.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/fuiweKX.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/JLoB2Tm.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/e6zqF7Z.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/83EMF2X.png'),
            $this->buildItemStructure($category, 'https://imgur.com/CYeRvj5.png'),
            $this->buildItemStructure($category, 'https://imgur.com/aIRHUMO.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/5GTmVFY.gif'),
        ];
    }
}
