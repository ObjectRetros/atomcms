<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasCineCategoryData
{
    public function getCineItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/JWzSQLa.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/Rr4ajkG.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/igQRc4R.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/pA8XHOU.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/n4y5tMX.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/xl4zv9x.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/s8g2vrY.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/6387aVh.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/nTSfbuB.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/n45C0lN.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/xzVtJLs.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/qdIpG80.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/K0Ar5Hr.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/2JAGZpr.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/t4pC6VD.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/H4Ph9Z6.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/nLJTPHo.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/ct4V3qF.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/yQvaC0M.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/L4icRgG.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/o2b8zyD.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/qFy6U9L.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/lIkrHeC.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/FrkBRnF.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/TsDyqps.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/PODBNvV.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/1NzaVih.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/ua4PVEw.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/xLRGCDJ.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/HGynaOb.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/9iSEagM.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/a6iAcig.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/MPhuI9j.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/8sOSziG.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/h0wtMzX.png'),
            $this->buildItemStructure($category, '/assets/images/home/items/OYGIW8N.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/mEeD2KO.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/YOXplAg.gif'),
            $this->buildItemStructure($category, '/assets/images/home/items/jK0fI0A.gif'),
        ];
    }
}
