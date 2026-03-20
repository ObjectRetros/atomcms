<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasArtistsCategoryData
{
    public function getArtistsItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/Zn69TxF.png'),
            $this->buildItemStructure($category, 'https://imgur.com/JYChc1s.png'),
            $this->buildItemStructure($category, 'https://imgur.com/9nAV7Uv.png'),
            $this->buildItemStructure($category, 'https://imgur.com/WBWYita.png'),
            $this->buildItemStructure($category, 'https://imgur.com/zgii3mv.png'),
            $this->buildItemStructure($category, 'https://imgur.com/eFY5GmS.png'),
            $this->buildItemStructure($category, 'https://imgur.com/cRGB9jR.png'),
            $this->buildItemStructure($category, 'https://imgur.com/bCkwdfV.png'),
            $this->buildItemStructure($category, 'https://imgur.com/f1YJnyR.png'),
            $this->buildItemStructure($category, 'https://imgur.com/gcrn1fZ.png'),
            $this->buildItemStructure($category, 'https://imgur.com/RZBJxcZ.png'),
            $this->buildItemStructure($category, 'https://imgur.com/mCmDqLI.png'),
            $this->buildItemStructure($category, 'https://imgur.com/cjcLgZN.png'),
            $this->buildItemStructure($category, 'https://imgur.com/v2WPg1D.png'),
            $this->buildItemStructure($category, 'https://imgur.com/RC8S7qy.png'),
            $this->buildItemStructure($category, 'https://imgur.com/Lm87PDS.png'),
            $this->buildItemStructure($category, 'https://imgur.com/zHzlXre.png'),
            $this->buildItemStructure($category, 'https://imgur.com/RSrua7L.png'),
            $this->buildItemStructure($category, 'https://imgur.com/rxB30lD.gif'),
        ];
    }
}
