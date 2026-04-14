<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasBlingAlphabetCategoryData
{
    public function getBlingAlphabetItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-a.png', 'Bling A'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-b.png', 'Bling B'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-c.png', 'Bling C'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-d.png', 'Bling D'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-e.png', 'Bling E'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-f.png', 'Bling F'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-g.png', 'Bling G'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-h.png', 'Bling H'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-i.png', 'Bling I'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-j.png', 'Bling J'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-k.png', 'Bling K'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-l.png', 'Bling L'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-m.png', 'Bling M'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-n.png', 'Bling N'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-o.png', 'Bling O'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-p.png', 'Bling P'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-q.png', 'Bling Q'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-r.png', 'Bling R'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-s.png', 'Bling S'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-t.png', 'Bling T'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-u.png', 'Bling U'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-v.png', 'Bling V'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-w.png', 'Bling W'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-x.png', 'Bling X'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-y.png', 'Bling Y'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-z.png', 'Bling Z'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-star.png', 'Bling Star'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-line.png', 'Bling Line'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-underscore.png', 'Bling Underscore'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-comma.png', 'Bling Comma'),
            $this->buildItemStructure($category, 'home-items/bling-alphabet-bling-dot.png', 'Bling Dot'),
        ];
    }
}
