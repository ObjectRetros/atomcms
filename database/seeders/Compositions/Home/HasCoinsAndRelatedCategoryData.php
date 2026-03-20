<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasCoinsAndRelatedCategoryData
{
    public function getCoinsAndRelatedItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/ua4PVEw.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/nTSfbuB.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/xzVtJLs.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/n45C0lN.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/yuCbyCi.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/JoAnVpH.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/Y3EfOaM.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/7PaD1Ah.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/2y9rc8b.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/y67yCaq.png'),
        ];
    }
}
