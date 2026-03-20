<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasSportsCategoryData
{
    public function getSportsItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/ptRKwXT.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/rSCx8jp.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/C2VHL3U.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/AcCN65j.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/Oxj1Mle.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/heJgbE2.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/rxM4tyj.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/JF8o6vJ.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/vyQrKrQ.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/usBF79P.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/FdnOmSB.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/yGfuoi7.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/R47z7pw.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/JR9csr6.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/afHlJ1R.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/LbY70Qn.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/CabeLDg.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/DaM3F4W.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/jGA5ikh.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/n1P8aEN.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/XTZ9VGM.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/MHYZnWm.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/7CtX9pS.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/IjHpCoK.gif'),
        ];
    }
}
