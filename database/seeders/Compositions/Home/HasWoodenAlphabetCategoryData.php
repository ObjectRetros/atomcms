<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasWoodenAlphabetCategoryData
{
    public function getWoodenAlphabetItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/rD9xRtS.png', 'Wooden A'),
            $this->buildItemStructure($category, 'https://imgur.com/ugX8JU4.png', 'Wooden A with Circle'),
            $this->buildItemStructure($category, 'https://imgur.com/jXTAUQ1.png', 'Wooden A with Dots'),
            $this->buildItemStructure($category, 'https://imgur.com/oDTz7SH.png', 'Wooden B'),
            $this->buildItemStructure($category, 'https://imgur.com/mve4r4L.png', 'Wooden C'),
            $this->buildItemStructure($category, 'https://imgur.com/7I8oh6q.png', 'Wooden D'),
            $this->buildItemStructure($category, 'https://imgur.com/AX7Bz81.png', 'Wooden E'),
            $this->buildItemStructure($category, 'https://imgur.com/39GJPuX.png', 'Wooden F'),
            $this->buildItemStructure($category, 'https://imgur.com/7eVZTkg.png', 'Wooden G'),
            $this->buildItemStructure($category, 'https://imgur.com/3sWi7A6.png', 'Wooden H'),
            $this->buildItemStructure($category, 'https://imgur.com/ci1S3st.png', 'Wooden I'),
            $this->buildItemStructure($category, 'https://imgur.com/egGtdgX.png', 'Wooden J'),
            $this->buildItemStructure($category, 'https://imgur.com/B5LRzQO.png', 'Wooden K'),
            $this->buildItemStructure($category, 'https://imgur.com/Mp5CEjG.png', 'Wooden L'),
            $this->buildItemStructure($category, 'https://imgur.com/nw48Hw0.png', 'Wooden M'),
            $this->buildItemStructure($category, 'https://imgur.com/2FBwCKd.png', 'Wooden N'),
            $this->buildItemStructure($category, 'https://imgur.com/1gNTqGa.png', 'Wooden O'),
            $this->buildItemStructure($category, 'https://imgur.com/iGAT7gk.png', 'Wooden O with Dots'),
            $this->buildItemStructure($category, 'https://imgur.com/tjn6zIl.png', 'Wooden P'),
            $this->buildItemStructure($category, 'https://imgur.com/fDi4M4C.png', 'Wooden Q'),
            $this->buildItemStructure($category, 'https://imgur.com/dj9hXeu.png', 'Wooden R'),
            $this->buildItemStructure($category, 'https://imgur.com/R0qgK7b.png', 'Wooden S'),
            $this->buildItemStructure($category, 'https://imgur.com/ZUbxiV5.png', 'Wooden T'),
            $this->buildItemStructure($category, 'https://imgur.com/S3aMyFu.png', 'Wooden U'),
            $this->buildItemStructure($category, 'https://imgur.com/Tu4j5p8.png', 'Wooden V'),
            $this->buildItemStructure($category, 'https://imgur.com/pffbyYo.png', 'Wooden W'),
            $this->buildItemStructure($category, 'https://imgur.com/gxs4vZI.png', 'Wooden X'),
            $this->buildItemStructure($category, 'https://imgur.com/UwFJRtb.png', 'Wooden Y'),
            $this->buildItemStructure($category, 'https://imgur.com/E0BvzIr.png', 'Wooden Z'),
            $this->buildItemStructure($category, 'https://imgur.com/lVgO0LQ.png', 'Wooden Exclamation'),
            $this->buildItemStructure($category, 'https://imgur.com/e6XshAT.png', 'Wooden Ascent 1'),
            $this->buildItemStructure($category, 'https://imgur.com/0VqdtYy.png', 'Wooden Ascent 2'),
            $this->buildItemStructure($category, 'https://imgur.com/ei91H19.png', 'Wooden Dot'),
            $this->buildItemStructure($category, 'https://imgur.com/UodfiS8.png', 'Wooden Question'),
            $this->buildItemStructure($category, 'https://imgur.com/i4h1loB.png', 'Wooden Comma'),
            $this->buildItemStructure($category, 'https://imgur.com/IjSVdCR.png', 'Wooden Underscore'),
        ];
    }
}
