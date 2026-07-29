<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ShopItemType: string implements HasLabel
{
    case Currency = 'currency';
    case Furniture = 'furniture';
    case Badge = 'badge';
    case Rank = 'rank';

    public function getLabel(): string
    {
        return match ($this) {
            self::Currency => 'Currency',
            self::Furniture => 'Furniture',
            self::Badge => 'Badge',
            self::Rank => 'Rank',
        };
    }
}
