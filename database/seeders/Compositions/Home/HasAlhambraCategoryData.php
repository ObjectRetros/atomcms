<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasAlhambraCategoryData
{
    public function getAlhambraItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/ggbQ2QG.png'),
            $this->buildItemStructure($category, 'https://imgur.com/BtN2Ten.gif'),
        ];
    }
}
