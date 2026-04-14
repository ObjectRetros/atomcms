<?php

namespace App\Actions\Shop;

use App\Actions\SendCurrency;
use App\Actions\SendFurniture;
use App\Models\Shop\WebsiteShopPackage;
use App\Models\User;
use App\Services\RconService;
use RuntimeException;

class FulfillPackage
{
    public function __construct(
        private RconService $rcon,
        private SendCurrency $sendCurrency,
        private SendFurniture $sendFurniture,
    ) {}

    public function execute(User $user, WebsiteShopPackage $package): void
    {
        $package->loadMissing('items');

        foreach ($package->items as $item) {
            $quantity = $item->pivot->quantity;

            match ($item->type) {
                'currency' => $this->giveCurrency($user, $item->type_value, $quantity),
                'furniture' => $this->giveFurniture($user, $item->type_value, $quantity),
                'badge' => $this->giveBadge($user, $item->type_value),
                'rank' => $this->giveRank($user, (int) $item->type_value),
                default => throw new RuntimeException("Unknown shop item type: {$item->type}"),
            };
        }
    }

    /**
     * @param  string  $typeValue  Format: "credits:100" or "duckets:50"
     */
    private function giveCurrency(User $user, string $typeValue, int $quantity): void
    {
        [$currencyType, $amount] = explode(':', $typeValue, 2);

        $this->sendCurrency->execute($user, $currencyType, (int) $amount * $quantity);
    }

    private function giveFurniture(User $user, string $itemId, int $quantity): void
    {
        $this->sendFurniture->execute($user, [
            ['item_id' => (int) $itemId, 'amount' => $quantity],
        ]);
    }

    private function giveBadge(User $user, string $badgeCode): void
    {
        if ($user->badges()->where('badge_code', $badgeCode)->exists()) {
            return;
        }

        if ($this->rcon->isConnected) {
            $this->rcon->giveBadge($user, $badgeCode);

            return;
        }

        $user->badges()->updateOrCreate([
            'user_id' => $user->id,
            'badge_code' => $badgeCode,
        ]);
    }

    private function giveRank(User $user, int $rankId): void
    {
        if ($this->rcon->isConnected) {
            $this->rcon->setRank($user, $rankId);
            $this->rcon->disconnectUser($user);
        } else {
            $user->update(['rank' => $rankId]);
        }
    }
}
