<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasPlasticAlphabetCategoryData
{
    public function getPlasticAlphabetItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/8UY0E1D.png', 'Plastic A'),
            $this->buildItemStructure($category, '/assets/images/home/items/gbIJIw3.png', 'Plastic A with Circle'),
            $this->buildItemStructure($category, '/assets/images/home/items/qQKLJLO.png', 'Plastic A with Dots'),
            $this->buildItemStructure($category, '/assets/images/home/items/PiYySn0.png', 'Plastic B'),
            $this->buildItemStructure($category, '/assets/images/home/items/M7KHAHi.png', 'Plastic C'),
            $this->buildItemStructure($category, '/assets/images/home/items/xJ7bpSR.png', 'Plastic D'),
            $this->buildItemStructure($category, '/assets/images/home/items/qCpb37j.png', 'Plastic E'),
            $this->buildItemStructure($category, '/assets/images/home/items/Ddo6APk.png', 'Plastic F'),
            $this->buildItemStructure($category, '/assets/images/home/items/ZxcckCA.png', 'Plastic G'),
            $this->buildItemStructure($category, '/assets/images/home/items/MHev0Wv.png', 'Plastic H'),
            $this->buildItemStructure($category, '/assets/images/home/items/ngqNEMc.png', 'Plastic I'),
            $this->buildItemStructure($category, '/assets/images/home/items/a1CZXfq.png', 'Plastic J'),
            $this->buildItemStructure($category, '/assets/images/home/items/jMNMRfU.png', 'Plastic K'),
            $this->buildItemStructure($category, '/assets/images/home/items/w3C1P33.png', 'Plastic L'),
            $this->buildItemStructure($category, '/assets/images/home/items/HjbrqPE.png', 'Plastic M'),
            $this->buildItemStructure($category, '/assets/images/home/items/GhKFwFH.png', 'Plastic N'),
            $this->buildItemStructure($category, '/assets/images/home/items/plKC1Wc.png', 'Plastic O'),
            $this->buildItemStructure($category, '/assets/images/home/items/oU0iMyT.png', 'Plastic O with Dots'),
            $this->buildItemStructure($category, '/assets/images/home/items/XjrUpyJ.png', 'Plastic P'),
            $this->buildItemStructure($category, '/assets/images/home/items/QrAfGdS.png', 'Plastic Q'),
            $this->buildItemStructure($category, '/assets/images/home/items/HqFL7H4.png', 'Plastic R'),
            $this->buildItemStructure($category, '/assets/images/home/items/wkLRVVJ.png', 'Plastic S'),
            $this->buildItemStructure($category, '/assets/images/home/items/MAx2KH6.png', 'Plastic T'),
            $this->buildItemStructure($category, '/assets/images/home/items/JMpn8Yv.png', 'Plastic U'),
            $this->buildItemStructure($category, '/assets/images/home/items/3IRMavb.png', 'Plastic V'),
            $this->buildItemStructure($category, '/assets/images/home/items/8WNtAdu.png', 'Plastic W'),
            $this->buildItemStructure($category, '/assets/images/home/items/Z2wiW7m.png', 'Plastic X'),
            $this->buildItemStructure($category, '/assets/images/home/items/DsnJ1WK.png', 'Plastic Y'),
            $this->buildItemStructure($category, '/assets/images/home/items/47LkrfV.png', 'Plastic Z'),
            $this->buildItemStructure($category, '/assets/images/home/items/Q1urMVp.png', 'Plastic Exclamation'),
            $this->buildItemStructure($category, '/assets/images/home/items/ozzHDhF.png', 'Plastic Ascent 1'),
            $this->buildItemStructure($category, '/assets/images/home/items/It4ypyn.png', 'Plastic Ascent 2'),
            $this->buildItemStructure($category, '/assets/images/home/items/oWWYlhP.png', 'Plastic Dot'),
            $this->buildItemStructure($category, '/assets/images/home/items/jOaPo0S.png', 'Plastic Star'),
            $this->buildItemStructure($category, '/assets/images/home/items/ktyfmfa.png', 'Plastic Underscore'),
        ];
    }
}
