<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasPaintingsCategoryData
{
    public function getPaintingsItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/lvP9mpi.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/n9pcILo.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/1H9vxRB.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/jBeREiH.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/RQWki4b.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/mOMARPv.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/zXC0mxX.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/Kn1PEj3.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/qVuS1dc.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/NvRbhEg.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/YgH9S4l.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/017MO7b.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/6n3cQfQ.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/S4eQiRU.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/0X72AMy.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/QyIPxXJ.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/mXITBHj.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/6aAEme6.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/ZaRCg5A.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/r8H4JP2.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/q3hCiTd.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/t2tOcM9.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/OBOfksG.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/nak2N2H.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/EvTbcfK.png'),
        ];
    }
}
