<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasClampsAndRelatedCategoryData
{
    public function getClampsAndRelatedItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/vHhHfrj.png'),
            $this->buildItemStructure($category, 'https://imgur.com/YFxCIvs.png'),
            $this->buildItemStructure($category, 'https://imgur.com/azcNMuS.png'),
            $this->buildItemStructure($category, 'https://imgur.com/2eTD55I.png'),
            $this->buildItemStructure($category, 'https://imgur.com/ZqPTdGY.png'),
            $this->buildItemStructure($category, 'https://imgur.com/NXUk5b4.png'),
            $this->buildItemStructure($category, 'https://imgur.com/UHpvkH1.png'),
            $this->buildItemStructure($category, 'https://imgur.com/Y8JWImU.png'),
            $this->buildItemStructure($category, 'https://imgur.com/xYh9WDY.png'),
            $this->buildItemStructure($category, 'https://imgur.com/L3X4hXn.png'),
            $this->buildItemStructure($category, 'https://imgur.com/VsAbCLA.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/GFKVz7T.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/hZEuVDr.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/iJehpuJ.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/5mbnoRs.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/tpkYez9.png'),
            $this->buildItemStructure($category, 'https://imgur.com/7n56hJR.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/4pSNBad.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/dupjwQd.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/IERo5E2.gif'),
        ];
    }
}
