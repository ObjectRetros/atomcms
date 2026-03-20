<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasBlingAlphabetCategoryData
{
    public function getBlingAlphabetItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/0LVEetW.png', 'Bling A'),
            $this->buildItemStructure($category, 'https://imgur.com/bX4ZhK4.png', 'Bling B'),
            $this->buildItemStructure($category, 'https://imgur.com/S3hvuUn.png', 'Bling C'),
            $this->buildItemStructure($category, 'https://imgur.com/awiZIAS.png', 'Bling D'),
            $this->buildItemStructure($category, 'https://imgur.com/VUqks2I.png', 'Bling E'),
            $this->buildItemStructure($category, 'https://imgur.com/nKddC1L.png', 'Bling F'),
            $this->buildItemStructure($category, 'https://imgur.com/wKHRpnp.png', 'Bling G'),
            $this->buildItemStructure($category, 'https://imgur.com/dr6FX9p.png', 'Bling H'),
            $this->buildItemStructure($category, 'https://imgur.com/Lb8JfL3.png', 'Bling I'),
            $this->buildItemStructure($category, 'https://imgur.com/Xf6VjvD.png', 'Bling J'),
            $this->buildItemStructure($category, 'https://imgur.com/uVUYtyt.png', 'Bling K'),
            $this->buildItemStructure($category, 'https://imgur.com/qtD654C.png', 'Bling L'),
            $this->buildItemStructure($category, 'https://imgur.com/w4cBaOR.png', 'Bling M'),
            $this->buildItemStructure($category, 'https://imgur.com/tVcQmqP.png', 'Bling N'),
            $this->buildItemStructure($category, 'https://imgur.com/Z5TsGY5.png', 'Bling O'),
            $this->buildItemStructure($category, 'https://imgur.com/RXNjPBz.png', 'Bling P'),
            $this->buildItemStructure($category, 'https://imgur.com/y7XRY03.png', 'Bling Q'),
            $this->buildItemStructure($category, 'https://imgur.com/Y9yCwdB.png', 'Bling R'),
            $this->buildItemStructure($category, 'https://imgur.com/fVKl4Jz.png', 'Bling S'),
            $this->buildItemStructure($category, 'https://imgur.com/PhjTvcA.png', 'Bling T'),
            $this->buildItemStructure($category, 'https://imgur.com/cM8Eg9g.png', 'Bling U'),
            $this->buildItemStructure($category, 'https://imgur.com/9FhesAC.png', 'Bling V'),
            $this->buildItemStructure($category, 'https://imgur.com/BDxRM5O.png', 'Bling W'),
            $this->buildItemStructure($category, 'https://imgur.com/0kZ92ag.png', 'Bling X'),
            $this->buildItemStructure($category, 'https://imgur.com/MvOPiLd.png', 'Bling Y'),
            $this->buildItemStructure($category, 'https://imgur.com/roOrqwg.png', 'Bling Z'),
            $this->buildItemStructure($category, 'https://imgur.com/t5bQVqG.png', 'Bling Star'),
            $this->buildItemStructure($category, 'https://imgur.com/XhKBGGB.png', 'Bling Line'),
            $this->buildItemStructure($category, 'https://imgur.com/krPGYKM.png', 'Bling Underscore'),
            $this->buildItemStructure($category, 'https://imgur.com/kxzRBVK.png', 'Bling Comma'),
            $this->buildItemStructure($category, 'https://imgur.com/nRfBWIC.png', 'Bling Dot'),
        ];
    }
}
