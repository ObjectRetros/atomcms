<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasCoinsAndRelatedCategoryData
{
    public function getCoinsAndRelatedItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/ua4PVEw.png'),
            $this->buildItemStructure($category, 'https://imgur.com/nTSfbuB.png'),
            $this->buildItemStructure($category, 'https://imgur.com/xzVtJLs.png'),
            $this->buildItemStructure($category, 'https://imgur.com/n45C0lN.png'),
            $this->buildItemStructure($category, 'https://imgur.com/yuCbyCi.png'),
            $this->buildItemStructure($category, 'https://imgur.com/JoAnVpH.png'),
            $this->buildItemStructure($category, 'https://imgur.com/Y3EfOaM.png'),
            $this->buildItemStructure($category, 'https://imgur.com/7PaD1Ah.png'),
            $this->buildItemStructure($category, 'https://imgur.com/2y9rc8b.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/y67yCaq.png'),
        ];
    }
}
