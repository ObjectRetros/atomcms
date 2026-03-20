<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasSportsCategoryData
{
    public function getSportsItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/ptRKwXT.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/rSCx8jp.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/C2VHL3U.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/AcCN65j.png'),
            $this->buildItemStructure($category, 'https://imgur.com/Oxj1Mle.png'),
            $this->buildItemStructure($category, 'https://imgur.com/heJgbE2.png'),
            $this->buildItemStructure($category, 'https://imgur.com/rxM4tyj.png'),
            $this->buildItemStructure($category, 'https://imgur.com/JF8o6vJ.png'),
            $this->buildItemStructure($category, 'https://imgur.com/vyQrKrQ.png'),
            $this->buildItemStructure($category, 'https://imgur.com/usBF79P.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/FdnOmSB.png'),
            $this->buildItemStructure($category, 'https://imgur.com/yGfuoi7.png'),
            $this->buildItemStructure($category, 'https://imgur.com/R47z7pw.png'),
            $this->buildItemStructure($category, 'https://imgur.com/JR9csr6.png'),
            $this->buildItemStructure($category, 'https://imgur.com/afHlJ1R.png'),
            $this->buildItemStructure($category, 'https://imgur.com/LbY70Qn.png'),
            $this->buildItemStructure($category, 'https://imgur.com/CabeLDg.png'),
            $this->buildItemStructure($category, 'https://imgur.com/DaM3F4W.png'),
            $this->buildItemStructure($category, 'https://imgur.com/jGA5ikh.png'),
            $this->buildItemStructure($category, 'https://imgur.com/n1P8aEN.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/XTZ9VGM.png'),
            $this->buildItemStructure($category, 'https://imgur.com/MHYZnWm.png'),
            $this->buildItemStructure($category, 'https://imgur.com/7CtX9pS.png'),
            $this->buildItemStructure($category, 'https://imgur.com/IjHpCoK.gif'),
        ];
    }
}
