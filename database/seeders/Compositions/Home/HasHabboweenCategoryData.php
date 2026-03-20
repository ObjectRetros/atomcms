<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasHabboweenCategoryData
{
    public function getHabboweenItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/UBHntOk.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/Ll30e0q.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/BXEDApT.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/J9GJMxr.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/D27F5lg.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/j0Z3qzG.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/toHwnTs.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/LpYgOnU.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/AnBYcrT.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/XDkh1vY.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/Xa6aZrA.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/eZjuDPo.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/ufHJLrT.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/oAoSSSZ.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/ZqHGfom.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/wf5uDHn.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/MO43bEQ.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/PXYlIcn.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/T5lXGt4.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/lhstMSq.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/AnyqIvz.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/qe3ALL6.gif'),
        ];
    }
}
