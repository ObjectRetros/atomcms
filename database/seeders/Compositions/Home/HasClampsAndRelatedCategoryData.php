<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasClampsAndRelatedCategoryData
{
    public function getClampsAndRelatedItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/vHhHfrj.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/YFxCIvs.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/azcNMuS.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/2eTD55I.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/ZqPTdGY.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/NXUk5b4.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/UHpvkH1.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/Y8JWImU.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/xYh9WDY.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/L3X4hXn.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/VsAbCLA.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/GFKVz7T.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/hZEuVDr.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/iJehpuJ.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/5mbnoRs.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/tpkYez9.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/7n56hJR.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/4pSNBad.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/dupjwQd.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/IERo5E2.gif'),
        ];
    }
}
