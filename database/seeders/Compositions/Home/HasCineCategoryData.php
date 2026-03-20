<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasCineCategoryData
{
    public function getCineItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://i.imgur.com/JWzSQLa.png'),
            $this->buildItemStructure($category, 'https://imgur.com/Rr4ajkG.png'),
            $this->buildItemStructure($category, 'https://imgur.com/igQRc4R.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/pA8XHOU.png'),
            $this->buildItemStructure($category, 'https://imgur.com/n4y5tMX.png'),
            $this->buildItemStructure($category, 'https://imgur.com/xl4zv9x.png'),
            $this->buildItemStructure($category, 'https://imgur.com/s8g2vrY.png'),
            $this->buildItemStructure($category, 'https://imgur.com/6387aVh.png'),
            $this->buildItemStructure($category, 'https://imgur.com/nTSfbuB.png'),
            $this->buildItemStructure($category, 'https://imgur.com/n45C0lN.png'),
            $this->buildItemStructure($category, 'https://imgur.com/xzVtJLs.png'),
            $this->buildItemStructure($category, 'https://i.imgur.com/qdIpG80.png'),
            $this->buildItemStructure($category, 'https://imgur.com/K0Ar5Hr.png'),
            $this->buildItemStructure($category, 'https://imgur.com/2JAGZpr.png'),
            $this->buildItemStructure($category, 'https://imgur.com/t4pC6VD.png'),
            $this->buildItemStructure($category, 'https://imgur.com/H4Ph9Z6.png'),
            $this->buildItemStructure($category, 'https://imgur.com/nLJTPHo.png'),
            $this->buildItemStructure($category, 'https://imgur.com/ct4V3qF.png'),
            $this->buildItemStructure($category, 'https://imgur.com/yQvaC0M.png'),
            $this->buildItemStructure($category, 'https://imgur.com/L4icRgG.png'),
            $this->buildItemStructure($category, 'https://imgur.com/o2b8zyD.png'),
            $this->buildItemStructure($category, 'https://imgur.com/qFy6U9L.png'),
            $this->buildItemStructure($category, 'https://imgur.com/lIkrHeC.png'),
            $this->buildItemStructure($category, 'https://imgur.com/FrkBRnF.png'),
            $this->buildItemStructure($category, 'https://imgur.com/TsDyqps.png'),
            $this->buildItemStructure($category, 'https://imgur.com/PODBNvV.png'),
            $this->buildItemStructure($category, 'https://imgur.com/1NzaVih.png'),
            $this->buildItemStructure($category, 'https://imgur.com/ua4PVEw.png'),
            $this->buildItemStructure($category, 'https://imgur.com/xLRGCDJ.png'),
            $this->buildItemStructure($category, 'https://imgur.com/HGynaOb.png'),
            $this->buildItemStructure($category, 'https://imgur.com/9iSEagM.png'),
            $this->buildItemStructure($category, 'https://imgur.com/a6iAcig.png'),
            $this->buildItemStructure($category, 'https://imgur.com/MPhuI9j.png'),
            $this->buildItemStructure($category, 'https://imgur.com/8sOSziG.png'),
            $this->buildItemStructure($category, 'https://imgur.com/h0wtMzX.png'),
            $this->buildItemStructure($category, 'https://imgur.com/OYGIW8N.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/mEeD2KO.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/YOXplAg.gif'),
            $this->buildItemStructure($category, 'https://imgur.com/jK0fI0A.gif'),
        ];
    }
}
