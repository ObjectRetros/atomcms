<?php

namespace App\Actions\Shop;

use App\Emulator\Contracts\BadgeRepository;
use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Contracts\FurnitureRepository;
use App\Enums\CurrencyTypes;
use App\Enums\ShopItemType;
use App\Exceptions\ShopPurchaseException;
use App\Models\Shop\WebsiteShopItem;
use App\Models\Shop\WebsiteShopPackage;
use App\Models\User;
use RuntimeException;

final readonly class FulfillPackage
{
    public function __construct(
        private readonly CurrencyRepository $currencies,
        private readonly FurnitureRepository $furniture,
        private readonly BadgeRepository $badges,
    ) {}

    /**
     * Deliver every item in the package to the user.
     *
     * @throws ShopPurchaseException when an item is misconfigured, so a
     *                               surrounding transaction rolls the purchase back
     */
    public function execute(User $user, WebsiteShopPackage $package): void
    {
        $package->loadMissing('items');

        if ($package->items->isEmpty()) {
            throw new ShopPurchaseException(__('This package is currently unavailable'));
        }

        foreach ($package->items as $item) {
            $pivot = $item->pivot;

            if ($pivot === null) {
                throw self::misconfigured($item);
            }

            $quantity = $pivot->quantity;

            if (! $item->is_active || $quantity < 1) {
                throw self::misconfigured($item);
            }

            match ($item->type) {
                ShopItemType::Currency => $this->giveCurrency($user, $item, $quantity),
                ShopItemType::Furniture => $this->giveFurniture($user, $item, $quantity),
                ShopItemType::Badge => $this->giveBadges($user, $item),
                ShopItemType::Rank => $this->giveRank($user, $item),
            };
        }
    }

    private function giveCurrency(User $user, WebsiteShopItem $item, int $quantity): void
    {
        // type_value format: "credits:100" or "duckets:50".
        if (! str_contains($item->type_value, ':')) {
            throw self::misconfigured($item);
        }

        [$currencyName, $amount] = explode(':', $item->type_value, 2);
        $currency = CurrencyTypes::fromCurrencyName($currencyName);
        $amount = $this->positiveInteger($item, $amount);

        if ($currency === null || $amount > intdiv(PHP_INT_MAX, $quantity)) {
            throw self::misconfigured($item);
        }

        $this->currencies->give($user, $currency, $amount * $quantity);
    }

    private function giveFurniture(User $user, WebsiteShopItem $item, int $quantity): void
    {
        $baseItemId = $this->positiveInteger($item, $item->type_value);

        $this->furniture->grant($user, $baseItemId, $quantity);
    }

    private function giveBadges(User $user, WebsiteShopItem $item): void
    {
        $codes = array_values(array_filter(array_map('trim', explode(';', $item->type_value))));

        if ($codes === []) {
            throw self::misconfigured($item);
        }

        foreach ($codes as $badge) {
            $this->badges->grant($user, $badge);
        }
    }

    private function giveRank(User $user, WebsiteShopItem $item): void
    {
        $rank = $this->positiveInteger($item, $item->type_value);

        $user->forceFill(['rank' => $rank])->save();
    }

    private function positiveInteger(WebsiteShopItem $item, string $value): int
    {
        $integer = filter_var(ltrim($value, '0'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if (! ctype_digit($value) || $integer === false) {
            throw self::misconfigured($item);
        }

        return $integer;
    }

    private static function misconfigured(WebsiteShopItem $item): ShopPurchaseException
    {
        report(new RuntimeException("Misconfigured shop item {$item->id}: {$item->type->value} => {$item->type_value}"));

        return new ShopPurchaseException(__('This package is currently unavailable'));
    }
}
