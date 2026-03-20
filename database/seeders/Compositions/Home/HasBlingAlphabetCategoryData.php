<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasBlingAlphabetCategoryData
{
    public function getBlingAlphabetItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/0LVEetW.png', 'Bling A'),
            $this->buildItemStructure($category, '/assets/images/home/items/bX4ZhK4.png', 'Bling B'),
            $this->buildItemStructure($category, '/assets/images/home/items/S3hvuUn.png', 'Bling C'),
            $this->buildItemStructure($category, '/assets/images/home/items/awiZIAS.png', 'Bling D'),
            $this->buildItemStructure($category, '/assets/images/home/items/VUqks2I.png', 'Bling E'),
            $this->buildItemStructure($category, '/assets/images/home/items/nKddC1L.png', 'Bling F'),
            $this->buildItemStructure($category, '/assets/images/home/items/wKHRpnp.png', 'Bling G'),
            $this->buildItemStructure($category, '/assets/images/home/items/dr6FX9p.png', 'Bling H'),
            $this->buildItemStructure($category, '/assets/images/home/items/Lb8JfL3.png', 'Bling I'),
            $this->buildItemStructure($category, '/assets/images/home/items/Xf6VjvD.png', 'Bling J'),
            $this->buildItemStructure($category, '/assets/images/home/items/uVUYtyt.png', 'Bling K'),
            $this->buildItemStructure($category, '/assets/images/home/items/qtD654C.png', 'Bling L'),
            $this->buildItemStructure($category, '/assets/images/home/items/w4cBaOR.png', 'Bling M'),
            $this->buildItemStructure($category, '/assets/images/home/items/tVcQmqP.png', 'Bling N'),
            $this->buildItemStructure($category, '/assets/images/home/items/Z5TsGY5.png', 'Bling O'),
            $this->buildItemStructure($category, '/assets/images/home/items/RXNjPBz.png', 'Bling P'),
            $this->buildItemStructure($category, '/assets/images/home/items/y7XRY03.png', 'Bling Q'),
            $this->buildItemStructure($category, '/assets/images/home/items/Y9yCwdB.png', 'Bling R'),
            $this->buildItemStructure($category, '/assets/images/home/items/fVKl4Jz.png', 'Bling S'),
            $this->buildItemStructure($category, '/assets/images/home/items/PhjTvcA.png', 'Bling T'),
            $this->buildItemStructure($category, '/assets/images/home/items/cM8Eg9g.png', 'Bling U'),
            $this->buildItemStructure($category, '/assets/images/home/items/9FhesAC.png', 'Bling V'),
            $this->buildItemStructure($category, '/assets/images/home/items/BDxRM5O.png', 'Bling W'),
            $this->buildItemStructure($category, '/assets/images/home/items/0kZ92ag.png', 'Bling X'),
            $this->buildItemStructure($category, '/assets/images/home/items/MvOPiLd.png', 'Bling Y'),
            $this->buildItemStructure($category, '/assets/images/home/items/roOrqwg.png', 'Bling Z'),
            $this->buildItemStructure($category, '/assets/images/home/items/t5bQVqG.png', 'Bling Star'),
            $this->buildItemStructure($category, '/assets/images/home/items/XhKBGGB.png', 'Bling Line'),
            $this->buildItemStructure($category, '/assets/images/home/items/krPGYKM.png', 'Bling Underscore'),
            $this->buildItemStructure($category, '/assets/images/home/items/kxzRBVK.png', 'Bling Comma'),
            $this->buildItemStructure($category, '/assets/images/home/items/nRfBWIC.png', 'Bling Dot'),
        ];
    }
}
