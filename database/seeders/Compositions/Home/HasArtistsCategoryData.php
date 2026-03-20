<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasArtistsCategoryData
{
    public function getArtistsItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'home-items/artists-item-1.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-2.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-3.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-4.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-5.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-6.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-7.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-8.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-9.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-10.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-11.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-12.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-13.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-14.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-15.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-16.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-17.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-18.png'),
            $this->buildItemStructure($category, 'home-items/artists-item-19.gif'),
        ];
    }
}
