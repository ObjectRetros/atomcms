<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasPiratesCategoryData
{
    public function getPiratesItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/8fCagJa.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/wSkXCCa.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/1ZB4VLh.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/wC7Y0uH.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/mb6G0i4.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/o4yQ5rs.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/KTVwNAQ.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/AuTN259.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/OZeaNx5.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/yuCbyCi.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/JoAnVpH.png'),
        ];
    }
}
