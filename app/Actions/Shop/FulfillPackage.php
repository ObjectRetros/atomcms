<?php

namespace App\Actions\Shop;

use App\Actions\SendBadges;
use App\Actions\SendCurrency;
use App\Actions\SendFurniture;
use App\Contracts\Rcon;
use App\Enums\CurrencyTypes;
use App\Exceptions\ShopPurchaseException;
use App\Models\Shop\WebsiteShopItem;
use App\Models\Shop\WebsiteShopPackage;
use App\Models\User;
use RuntimeException;

class FulfillPackage
{
    public function __construct(
        private readonly Rcon $rcon,
        private readonly SendCurrency $sendCurrency,
        private readonly SendFurniture $sendFurniture,
        private readonly SendBadges $sendBadges,
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

        foreach ($package->items as $item) {
            $pivot = $item->pivot;

            if ($pivot === null) {
                throw self::misconfigured($item);
            }

            $quantity = $pivot->quantity;

            match ($item->type) {
                'currency' => $this->giveCurrency($user, $item, $quantity),
                'furniture' => $this->giveFurniture($user, $item, $quantity),
                'badge' => $this->sendBadges->execute($user, $item->type_value),
                'rank' => $this->giveRank($user, (int) $item->type_value),
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

        $this->sendCurrency->execute($user, $currency, (int) $amount * $quantity);
    }

    private function giveFurniture(User $user, WebsiteShopItem $item, int $quantity): void
    {
        $this->sendFurniture->execute($user, [
            ['item_id' => (int) $item->type_value, 'amount' => $quantity],
        ]);
    }

    private function giveRank(User $user, int $rank): void
    {
        if (! $this->rcon->isConnected()) {
            $user->update(['rank' => $rank]);

            return;
        }

        $this->rcon->setRank($user, $rank);
        $this->rcon->disconnectUser($user);
    }

    private static function misconfigured(WebsiteShopItem $item): ShopPurchaseException
    {
        report(new RuntimeException("Misconfigured shop item {$item->id}: {$item->type} => {$item->type_value}"));

        return new ShopPurchaseException(__('This package is currently unavailable'));
    }
}
