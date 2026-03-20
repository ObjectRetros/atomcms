<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasButtonsCategoryData
{
    public function getButtonsItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/Ddhqe7b.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/hNtWd9E.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/brFe31C.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/3kVCPYl.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/DTahSvL.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/vixHU7Y.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/tNqwijk.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/tR87uPy.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/FllBdQi.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/yrcJctl.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/OUUH66i.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/eKbJBrt.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/lMvhRBP.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/hynUlAW.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/uEsqj0u.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/SKnztj3.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/94UzHFD.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/MHuNybt.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/YxWT1uT.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/7hp5bKr.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/or6mX6H.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/qqy5awt.png'),
        ];
    }
}
