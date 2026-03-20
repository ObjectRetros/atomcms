<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasWoodenAlphabetCategoryData
{
    public function getWoodenAlphabetItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-a.png', 'Wooden A'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-a-with-circle.png', 'Wooden A with Circle'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-a-with-dots.png', 'Wooden A with Dots'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-b.png', 'Wooden B'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-c.png', 'Wooden C'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-d.png', 'Wooden D'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-e.png', 'Wooden E'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-f.png', 'Wooden F'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-g.png', 'Wooden G'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-h.png', 'Wooden H'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-i.png', 'Wooden I'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-j.png', 'Wooden J'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-k.png', 'Wooden K'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-l.png', 'Wooden L'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-m.png', 'Wooden M'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-n.png', 'Wooden N'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-o.png', 'Wooden O'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-o-with-dots.png', 'Wooden O with Dots'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-p.png', 'Wooden P'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-q.png', 'Wooden Q'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-r.png', 'Wooden R'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-s.png', 'Wooden S'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-t.png', 'Wooden T'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-u.png', 'Wooden U'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-v.png', 'Wooden V'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-w.png', 'Wooden W'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-x.png', 'Wooden X'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-y.png', 'Wooden Y'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-z.png', 'Wooden Z'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-exclamation.png', 'Wooden Exclamation'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-ascent-1.png', 'Wooden Ascent 1'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-ascent-2.png', 'Wooden Ascent 2'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-dot.png', 'Wooden Dot'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-question.png', 'Wooden Question'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-comma.png', 'Wooden Comma'),
            $this->buildItemStructure($category, '/assets/images/home/items/wooden-alphabet-wooden-underscore.png', 'Wooden Underscore'),
        ];
    }
}
