<?php

namespace App\Actions\Shop;

use App\Emulator\Contracts\BadgeRepository;
use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Contracts\FurnitureRepository;
use App\Enums\CurrencyTypes;
use App\Exceptions\ShopPurchaseException;
use App\Models\Shop\WebsiteShopItem;
use App\Models\Shop\WebsiteShopPackage;
use App\Models\User;
use RuntimeException;

class FulfillPackage
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
                'currency' => $this->giveCurrency($user, $item, $quantity),
                'furniture' => $this->giveFurniture($user, $item, $quantity),
                'badge' => $this->giveBadges($user, $item),
                'rank' => $this->giveRank($user, $item),
                default => throw self::misconfigured($item),
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

        if ($currency === null || (int) $amount <= 0) {
            throw self::misconfigured($item);
        }

        $this->currencies->give($user, $currency, (int) $amount * $quantity);
    }

    private function giveFurniture(User $user, WebsiteShopItem $item, int $quantity): void
    {
        $baseItemId = (int) $item->type_value;

        if ($baseItemId <= 0) {
            throw self::misconfigured($item);
        }

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
        $rank = (int) $item->type_value;

        if ($rank <= 0) {
            throw self::misconfigured($item);
        }

        $user->update(['rank' => $rank]);
    }

    private static function misconfigured(WebsiteShopItem $item): ShopPurchaseException
    {
        report(new RuntimeException("Misconfigured shop item {$item->id}: {$item->type} => {$item->type_value}"));

        return new ShopPurchaseException(__('This package is currently unavailable'));
    }
}
