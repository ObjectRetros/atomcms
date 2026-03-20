<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasPaintingsCategoryData
{
    public function getPaintingsItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/lvP9mpi.png'),
            $this->buildItemStructure($category, 'https://imgur.com/n9pcILo.png'),
            $this->buildItemStructure($category, 'https://imgur.com/1H9vxRB.png'),
            $this->buildItemStructure($category, 'https://imgur.com/jBeREiH.png'),
            $this->buildItemStructure($category, 'https://imgur.com/RQWki4b.png'),
            $this->buildItemStructure($category, 'https://imgur.com/mOMARPv.png'),
            $this->buildItemStructure($category, 'https://imgur.com/zXC0mxX.png'),
            $this->buildItemStructure($category, 'https://imgur.com/Kn1PEj3.png'),
            $this->buildItemStructure($category, 'https://imgur.com/qVuS1dc.png'),
            $this->buildItemStructure($category, 'https://imgur.com/NvRbhEg.png'),
            $this->buildItemStructure($category, 'https://imgur.com/YgH9S4l.png'),
            $this->buildItemStructure($category, 'https://imgur.com/017MO7b.png'),
            $this->buildItemStructure($category, 'https://imgur.com/6n3cQfQ.png'),
            $this->buildItemStructure($category, 'https://imgur.com/S4eQiRU.png'),
            $this->buildItemStructure($category, 'https://imgur.com/0X72AMy.png'),
            $this->buildItemStructure($category, 'https://imgur.com/QyIPxXJ.png'),
            $this->buildItemStructure($category, 'https://imgur.com/mXITBHj.png'),
            $this->buildItemStructure($category, 'https://imgur.com/6aAEme6.png'),
            $this->buildItemStructure($category, 'https://imgur.com/ZaRCg5A.png'),
            $this->buildItemStructure($category, 'https://imgur.com/r8H4JP2.png'),
            $this->buildItemStructure($category, 'https://imgur.com/q3hCiTd.png'),
            $this->buildItemStructure($category, 'https://imgur.com/t2tOcM9.png'),
            $this->buildItemStructure($category, 'https://imgur.com/OBOfksG.png'),
            $this->buildItemStructure($category, 'https://imgur.com/nak2N2H.png'),
            $this->buildItemStructure($category, 'https://imgur.com/EvTbcfK.png'),
        ];
    }
}
