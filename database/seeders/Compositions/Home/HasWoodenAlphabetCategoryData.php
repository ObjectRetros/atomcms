<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasWoodenAlphabetCategoryData
{
    public function getWoodenAlphabetItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/rD9xRtS.png', 'Wooden A'),
            $this->buildItemStructure($category, '/assets/images/home/items/ugX8JU4.png', 'Wooden A with Circle'),
            $this->buildItemStructure($category, '/assets/images/home/items/jXTAUQ1.png', 'Wooden A with Dots'),
            $this->buildItemStructure($category, '/assets/images/home/items/oDTz7SH.png', 'Wooden B'),
            $this->buildItemStructure($category, '/assets/images/home/items/mve4r4L.png', 'Wooden C'),
            $this->buildItemStructure($category, '/assets/images/home/items/7I8oh6q.png', 'Wooden D'),
            $this->buildItemStructure($category, '/assets/images/home/items/AX7Bz81.png', 'Wooden E'),
            $this->buildItemStructure($category, '/assets/images/home/items/39GJPuX.png', 'Wooden F'),
            $this->buildItemStructure($category, '/assets/images/home/items/7eVZTkg.png', 'Wooden G'),
            $this->buildItemStructure($category, '/assets/images/home/items/3sWi7A6.png', 'Wooden H'),
            $this->buildItemStructure($category, '/assets/images/home/items/ci1S3st.png', 'Wooden I'),
            $this->buildItemStructure($category, '/assets/images/home/items/egGtdgX.png', 'Wooden J'),
            $this->buildItemStructure($category, '/assets/images/home/items/B5LRzQO.png', 'Wooden K'),
            $this->buildItemStructure($category, '/assets/images/home/items/Mp5CEjG.png', 'Wooden L'),
            $this->buildItemStructure($category, '/assets/images/home/items/nw48Hw0.png', 'Wooden M'),
            $this->buildItemStructure($category, '/assets/images/home/items/2FBwCKd.png', 'Wooden N'),
            $this->buildItemStructure($category, '/assets/images/home/items/1gNTqGa.png', 'Wooden O'),
            $this->buildItemStructure($category, '/assets/images/home/items/iGAT7gk.png', 'Wooden O with Dots'),
            $this->buildItemStructure($category, '/assets/images/home/items/tjn6zIl.png', 'Wooden P'),
            $this->buildItemStructure($category, '/assets/images/home/items/fDi4M4C.png', 'Wooden Q'),
            $this->buildItemStructure($category, '/assets/images/home/items/dj9hXeu.png', 'Wooden R'),
            $this->buildItemStructure($category, '/assets/images/home/items/R0qgK7b.png', 'Wooden S'),
            $this->buildItemStructure($category, '/assets/images/home/items/ZUbxiV5.png', 'Wooden T'),
            $this->buildItemStructure($category, '/assets/images/home/items/S3aMyFu.png', 'Wooden U'),
            $this->buildItemStructure($category, '/assets/images/home/items/Tu4j5p8.png', 'Wooden V'),
            $this->buildItemStructure($category, '/assets/images/home/items/pffbyYo.png', 'Wooden W'),
            $this->buildItemStructure($category, '/assets/images/home/items/gxs4vZI.png', 'Wooden X'),
            $this->buildItemStructure($category, '/assets/images/home/items/UwFJRtb.png', 'Wooden Y'),
            $this->buildItemStructure($category, '/assets/images/home/items/E0BvzIr.png', 'Wooden Z'),
            $this->buildItemStructure($category, '/assets/images/home/items/lVgO0LQ.png', 'Wooden Exclamation'),
            $this->buildItemStructure($category, '/assets/images/home/items/e6XshAT.png', 'Wooden Ascent 1'),
            $this->buildItemStructure($category, '/assets/images/home/items/0VqdtYy.png', 'Wooden Ascent 2'),
            $this->buildItemStructure($category, '/assets/images/home/items/ei91H19.png', 'Wooden Dot'),
            $this->buildItemStructure($category, '/assets/images/home/items/UodfiS8.png', 'Wooden Question'),
            $this->buildItemStructure($category, '/assets/images/home/items/i4h1loB.png', 'Wooden Comma'),
            $this->buildItemStructure($category, '/assets/images/home/items/IjSVdCR.png', 'Wooden Underscore'),
        ];
    }
}
