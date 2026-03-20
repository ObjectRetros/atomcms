<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasArtistsCategoryData
{
    public function getArtistsItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/Zn69TxF.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/JYChc1s.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/9nAV7Uv.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/WBWYita.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/zgii3mv.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/eFY5GmS.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/cRGB9jR.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/bCkwdfV.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/f1YJnyR.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/gcrn1fZ.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/RZBJxcZ.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/mCmDqLI.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/cjcLgZN.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/v2WPg1D.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/RC8S7qy.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/Lm87PDS.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/zHzlXre.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/RSrua7L.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/rxB30lD.gif'),
        ];
    }
}
