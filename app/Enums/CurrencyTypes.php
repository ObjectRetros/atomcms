<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum CurrencyTypes: int
{
    use HasOptions;

    case Credits = -1;
    case Duckets = 0;
    case Diamonds = 5;
    case Points = 101;

    public static function fromCurrencyName(string $currencyName): ?self
    {
        return match (strtolower($currencyName)) {
            'credits' => self::Credits,
            'duckets' => self::Duckets,
            'diamonds' => self::Diamonds,
            'points' => self::Points,
            default => null,
        };
    }

    public function getImage(): string
    {
        return match ($this) {
            self::Credits => asset('assets/images/currencies/credits.gif'),
            self::Duckets => asset('assets/images/currencies/duckets.png'),
            self::Diamonds => asset('assets/images/currencies/diamonds.png'),
            self::Points => asset('assets/images/currencies/points.png'),
        };
    }
}
