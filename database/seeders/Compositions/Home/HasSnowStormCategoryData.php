<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasSnowStormCategoryData
{
    public function getSnowStormItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/ihF42Jg.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/zguUKo8.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/8PmwxST.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/H7Qv3xD.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/rMB2adu.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/3FNfWfL.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/KU3KhuT.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/DfFNkGv.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/yunDkul.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/xbGkm0T.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/vudQcoa.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/lfCBnwE.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/QGngJNl.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/W7pNV3e.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/wvYJ8qA.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/GQLep2j.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/MRwLpIP.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/vySZm2M.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/YAnI4pm.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/Hn9JmRw.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/A9XZpH2.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/7N2f44D.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/VkORDBT.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/4YPllf0.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/aqFxWF2.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/gMiKnII.png'),
        ];
    }
}
