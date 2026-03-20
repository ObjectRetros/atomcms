<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasHabboweenCategoryData
{
    public function getHabboweenItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/UBHntOk.png'),
            $this->buildItemStructure($category, 'https://imgur.com/Ll30e0q.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/BXEDApT.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/J9GJMxr.png'),
            $this->buildItemStructure($category, 'https://imgur.com/D27F5lg.png'),
            $this->buildItemStructure($category, 'https://imgur.com/j0Z3qzG.png'),
            $this->buildItemStructure($category, 'https://imgur.com/toHwnTs.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/LpYgOnU.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/AnBYcrT.png'),
            $this->buildItemStructure($category, 'https://imgur.com/XDkh1vY.png'),
            $this->buildItemStructure($category, 'https://imgur.com/Xa6aZrA.png'),
            $this->buildItemStructure($category, 'https://imgur.com/eZjuDPo.png'),
            $this->buildItemStructure($category, 'https://imgur.com/ufHJLrT.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/oAoSSSZ.png'),
            $this->buildItemStructure($category, 'https://imgur.com/ZqHGfom.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/wf5uDHn.png'),
            $this->buildItemStructure($category, 'https://imgur.com/MO43bEQ.png'),
            $this->buildItemStructure($category, 'https://imgur.com/PXYlIcn.png'),
            $this->buildItemStructure($category, 'https://imgur.com/T5lXGt4.png'),
            $this->buildItemStructure($category, 'https://imgur.com/lhstMSq.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/AnyqIvz.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/qe3ALL6.gif'),
        ];
    }
}
