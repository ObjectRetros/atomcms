<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasSnowStormCategoryData
{
    public function getSnowStormItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/ihF42Jg.png'),
            $this->buildItemStructure($category, 'https://imgur.com/zguUKo8.png'),
            $this->buildItemStructure($category, 'https://imgur.com/8PmwxST.png'),
            $this->buildItemStructure($category, 'https://imgur.com/H7Qv3xD.png'),
            $this->buildItemStructure($category, 'https://imgur.com/rMB2adu.png'),
            $this->buildItemStructure($category, 'https://imgur.com/3FNfWfL.png'),
            $this->buildItemStructure($category, 'https://imgur.com/KU3KhuT.png'),
            $this->buildItemStructure($category, 'https://imgur.com/DfFNkGv.png'),
            $this->buildItemStructure($category, 'https://imgur.com/yunDkul.png'),
            $this->buildItemStructure($category, 'https://imgur.com/xbGkm0T.png'),
            $this->buildItemStructure($category, 'https://imgur.com/vudQcoa.png'),
            $this->buildItemStructure($category, 'https://imgur.com/lfCBnwE.png'),
            $this->buildItemStructure($category, 'https://imgur.com/QGngJNl.png'),
            $this->buildItemStructure($category, 'https://imgur.com/W7pNV3e.png'),
            $this->buildItemStructure($category, 'https://imgur.com/wvYJ8qA.png'),
            $this->buildItemStructure($category, 'https://imgur.com/GQLep2j.png'),
            $this->buildItemStructure($category, 'https://imgur.com/MRwLpIP.png'),
            $this->buildItemStructure($category, 'https://imgur.com/vySZm2M.png'),
            $this->buildItemStructure($category, 'https://imgur.com/YAnI4pm.png'),
            $this->buildItemStructure($category, 'https://imgur.com/Hn9JmRw.png'),
            $this->buildItemStructure($category, 'https://imgur.com/A9XZpH2.png'),
            $this->buildItemStructure($category, 'https://imgur.com/7N2f44D.png'),
            $this->buildItemStructure($category, 'https://imgur.com/VkORDBT.png'),
            $this->buildItemStructure($category, 'https://imgur.com/4YPllf0.png'),
            $this->buildItemStructure($category, 'https://imgur.com/aqFxWF2.png'),
            $this->buildItemStructure($category, 'https://imgur.com/gMiKnII.png'),
        ];
    }
}
