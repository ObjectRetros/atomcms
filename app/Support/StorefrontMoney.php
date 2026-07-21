<?php

namespace App\Support;

use Brick\Money\Currency;
use Brick\Money\Money;

final class StorefrontMoney
{
    public static function currencyCode(?string $currency = null): string
    {
        $code = strtoupper(trim($currency ?? (string) config('habbo.paypal.currency', 'USD')));

        return Currency::of($code)->getCurrencyCode();
    }

    public static function fromMinor(int $amount, ?string $currency = null): Money
    {
        return Money::ofMinor($amount, self::currencyCode($currency));
    }

    public static function fromMajor(int $amount, ?string $currency = null): Money
    {
        return Money::of($amount, self::currencyCode($currency));
    }

    public static function fromDecimal(string $amount, string $currency): Money
    {
        return Money::of($amount, self::currencyCode($currency));
    }

    public static function minorAmount(Money $money): int
    {
        return $money->getMinorAmount()->toInt();
    }

    public static function decimalAmount(int $minorAmount, ?string $currency = null): string
    {
        return (string) self::fromMinor($minorAmount, $currency)->getAmount();
    }

    public static function format(int $minorAmount, ?string $currency = null): string
    {
        return (string) self::fromMinor($minorAmount, $currency);
    }
}
