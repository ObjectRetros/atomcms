<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasPiratesCategoryData
{
    public function getPiratesItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/8fCagJa.png'),
            $this->buildItemStructure($category, 'https://imgur.com/wSkXCCa.png'),
            $this->buildItemStructure($category, 'https://imgur.com/1ZB4VLh.png'),
            $this->buildItemStructure($category, 'https://imgur.com/wC7Y0uH.png'),
            $this->buildItemStructure($category, 'https://imgur.com/mb6G0i4.png'),
            $this->buildItemStructure($category, 'https://imgur.com/o4yQ5rs.png'),
            $this->buildItemStructure($category, 'https://imgur.com/KTVwNAQ.png'),
            $this->buildItemStructure($category, 'https://imgur.com/AuTN259.png'),
            $this->buildItemStructure($category, 'https://imgur.com/OZeaNx5.png'),
            $this->buildItemStructure($category, 'https://imgur.com/yuCbyCi.png'),
            $this->buildItemStructure($category, 'https://imgur.com/JoAnVpH.png'),
        ];
    }
}
