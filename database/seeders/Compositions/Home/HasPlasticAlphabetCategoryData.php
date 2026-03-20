<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasPlasticAlphabetCategoryData
{
    public function getPlasticAlphabetItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-a.png', 'Plastic A'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-a-with-circle.png', 'Plastic A with Circle'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-a-with-dots.png', 'Plastic A with Dots'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-b.png', 'Plastic B'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-c.png', 'Plastic C'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-d.png', 'Plastic D'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-e.png', 'Plastic E'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-f.png', 'Plastic F'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-g.png', 'Plastic G'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-h.png', 'Plastic H'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-i.png', 'Plastic I'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-j.png', 'Plastic J'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-k.png', 'Plastic K'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-l.png', 'Plastic L'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-m.png', 'Plastic M'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-n.png', 'Plastic N'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-o.png', 'Plastic O'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-o-with-dots.png', 'Plastic O with Dots'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-p.png', 'Plastic P'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-q.png', 'Plastic Q'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-r.png', 'Plastic R'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-s.png', 'Plastic S'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-t.png', 'Plastic T'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-u.png', 'Plastic U'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-v.png', 'Plastic V'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-w.png', 'Plastic W'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-x.png', 'Plastic X'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-y.png', 'Plastic Y'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-z.png', 'Plastic Z'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-exclamation.png', 'Plastic Exclamation'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-ascent-1.png', 'Plastic Ascent 1'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-ascent-2.png', 'Plastic Ascent 2'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-dot.png', 'Plastic Dot'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-star.png', 'Plastic Star'),
            $this->buildItemStructure($category, '/assets/images/home/items/plastic-alphabet-plastic-underscore.png', 'Plastic Underscore'),
        ];
    }
}
