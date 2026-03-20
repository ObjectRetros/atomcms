<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasPlasticAlphabetCategoryData
{
    public function getPlasticAlphabetItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/8UY0E1D.png', 'Plastic A'),
            $this->buildItemStructure($category, 'https://imgur.com/gbIJIw3.png', 'Plastic A with Circle'),
            $this->buildItemStructure($category, 'https://imgur.com/qQKLJLO.png', 'Plastic A with Dots'),
            $this->buildItemStructure($category, 'https://imgur.com/PiYySn0.png', 'Plastic B'),
            $this->buildItemStructure($category, 'https://imgur.com/M7KHAHi.png', 'Plastic C'),
            $this->buildItemStructure($category, 'https://imgur.com/xJ7bpSR.png', 'Plastic D'),
            $this->buildItemStructure($category, 'https://imgur.com/qCpb37j.png', 'Plastic E'),
            $this->buildItemStructure($category, 'https://imgur.com/Ddo6APk.png', 'Plastic F'),
            $this->buildItemStructure($category, 'https://imgur.com/ZxcckCA.png', 'Plastic G'),
            $this->buildItemStructure($category, 'https://imgur.com/MHev0Wv.png', 'Plastic H'),
            $this->buildItemStructure($category, 'https://imgur.com/ngqNEMc.png', 'Plastic I'),
            $this->buildItemStructure($category, 'https://imgur.com/a1CZXfq.png', 'Plastic J'),
            $this->buildItemStructure($category, 'https://imgur.com/jMNMRfU.png', 'Plastic K'),
            $this->buildItemStructure($category, 'https://imgur.com/w3C1P33.png', 'Plastic L'),
            $this->buildItemStructure($category, 'https://imgur.com/HjbrqPE.png', 'Plastic M'),
            $this->buildItemStructure($category, 'https://imgur.com/GhKFwFH.png', 'Plastic N'),
            $this->buildItemStructure($category, 'https://imgur.com/plKC1Wc.png', 'Plastic O'),
            $this->buildItemStructure($category, 'https://imgur.com/oU0iMyT.png', 'Plastic O with Dots'),
            $this->buildItemStructure($category, 'https://imgur.com/XjrUpyJ.png', 'Plastic P'),
            $this->buildItemStructure($category, 'https://imgur.com/QrAfGdS.png', 'Plastic Q'),
            $this->buildItemStructure($category, 'https://imgur.com/HqFL7H4.png', 'Plastic R'),
            $this->buildItemStructure($category, 'https://imgur.com/wkLRVVJ.png', 'Plastic S'),
            $this->buildItemStructure($category, 'https://imgur.com/MAx2KH6.png', 'Plastic T'),
            $this->buildItemStructure($category, 'https://imgur.com/JMpn8Yv.png', 'Plastic U'),
            $this->buildItemStructure($category, 'https://imgur.com/3IRMavb.png', 'Plastic V'),
            $this->buildItemStructure($category, 'https://imgur.com/8WNtAdu.png', 'Plastic W'),
            $this->buildItemStructure($category, 'https://imgur.com/Z2wiW7m.png', 'Plastic X'),
            $this->buildItemStructure($category, 'https://imgur.com/DsnJ1WK.png', 'Plastic Y'),
            $this->buildItemStructure($category, 'https://imgur.com/47LkrfV.png', 'Plastic Z'),
            $this->buildItemStructure($category, 'https://imgur.com/Q1urMVp.png', 'Plastic Exclamation'),
            $this->buildItemStructure($category, 'https://imgur.com/ozzHDhF.png', 'Plastic Ascent 1'),
            $this->buildItemStructure($category, 'https://imgur.com/It4ypyn.png', 'Plastic Ascent 2'),
            $this->buildItemStructure($category, 'https://imgur.com/oWWYlhP.png', 'Plastic Dot'),
            $this->buildItemStructure($category, 'https://imgur.com/jOaPo0S.png', 'Plastic Star'),
            $this->buildItemStructure($category, 'https://imgur.com/ktyfmfa.png', 'Plastic Underscore'),
        ];
    }
}
