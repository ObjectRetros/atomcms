<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasButtonsCategoryData
{
    public function getButtonsItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/Ddhqe7b.png'),
            $this->buildItemStructure($category, 'https://imgur.com/hNtWd9E.png'),
            $this->buildItemStructure($category, 'https://imgur.com/brFe31C.png'),
            $this->buildItemStructure($category, 'https://imgur.com/3kVCPYl.png'),
            $this->buildItemStructure($category, 'https://imgur.com/DTahSvL.png'),
            $this->buildItemStructure($category, 'https://imgur.com/vixHU7Y.png'),
            $this->buildItemStructure($category, 'https://imgur.com/tNqwijk.png'),
            $this->buildItemStructure($category, 'https://imgur.com/tR87uPy.png'),
            $this->buildItemStructure($category, 'https://imgur.com/FllBdQi.png'),
            $this->buildItemStructure($category, 'https://imgur.com/yrcJctl.png'),
            $this->buildItemStructure($category, 'https://imgur.com/OUUH66i.png'),
            $this->buildItemStructure($category, 'https://imgur.com/eKbJBrt.png'),
            $this->buildItemStructure($category, 'https://imgur.com/lMvhRBP.png'),
            $this->buildItemStructure($category, 'https://imgur.com/hynUlAW.png'),
            $this->buildItemStructure($category, 'https://imgur.com/uEsqj0u.png'),
            $this->buildItemStructure($category, 'https://imgur.com/SKnztj3.png'),
            $this->buildItemStructure($category, 'https://imgur.com/94UzHFD.png'),
            $this->buildItemStructure($category, 'https://imgur.com/MHuNybt.png'),
            $this->buildItemStructure($category, 'https://imgur.com/YxWT1uT.png'),
            $this->buildItemStructure($category, 'https://imgur.com/7hp5bKr.png'),
            $this->buildItemStructure($category, 'https://imgur.com/or6mX6H.png'),
            $this->buildItemStructure($category, 'https://imgur.com/qqy5awt.png'),
        ];
    }
}
