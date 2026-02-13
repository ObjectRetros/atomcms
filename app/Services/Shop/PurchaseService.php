<?php

namespace App\Services\Shop;

use App\Actions\SendCurrency;
use App\Actions\SendFurniture;
use App\Models\Shop\WebsiteShopArticle;
use App\Models\User;
use App\Services\RconService;

class PurchaseService
{
    public function __construct(
        private readonly RconService $rcon,
        private readonly BadgeService $badgeService,
    ) {}

    public function processPackage(WebsiteShopArticle $package, User $recipient, ?User $sender = null): void
    {
        $this->applyRank($package, $recipient);

        $this->sendCurrency($recipient, $package);

        if ($package->badges) {
            $this->badgeService->giveToUser($recipient, $package->badges);
        }

        if ($package->furniture) {
            $this->sendFurniture($recipient, $package->furniture);
        }
    }

    public function validatePurchase(WebsiteShopArticle $package, User $buyer, ?User $recipient = null): array
    {
        $recipient ??= $buyer;

        if ($this->isGiftAttempt($buyer, $recipient) && ! $package->is_giftable) {
            return ['valid' => false, 'message' => __('This package is not giftable')];
        }

        if ($package->give_rank && $recipient->rank >= $package->give_rank) {
            $message = $this->isGiftAttempt($buyer, $recipient)
                ? __('The recipient is already this or a higher rank')
                : __('You are already this or a higher rank');

            return ['valid' => false, 'message' => $message];
        }

        if (! $this->rcon->isConnected && $recipient->online === '1') {
            return ['valid' => false, 'message' => __('Please logout before purchasing a package')];
        }

        if ($buyer->website_balance < $package->price()) {
            $amount = $package->price() - $buyer->website_balance;

            return ['valid' => false, 'message' => __('You need to top-up your account with another $:amount to purchase this package', ['amount' => $amount])];
        }

        return ['valid' => true];
    }

    public function deductBalance(User $buyer, int $amount): void
    {
        $buyer->decrement('website_balance', $amount);
    }

    public function getSuccessMessage(WebsiteShopArticle $package, User $buyer, User $recipient): string
    {
        if ($this->isGiftAttempt($buyer, $recipient)) {
            return __('You have successfully purchased the package :name for :username', [
                'name' => $package->name,
                'username' => $recipient->username,
            ]);
        }

        return __('You have successfully purchased the package :name', ['name' => $package->name]);
    }

    private function applyRank(WebsiteShopArticle $package, User $recipient): void
    {
        if (! $package->give_rank) {
            return;
        }

        if ($this->rcon->isConnected) {
            $this->rcon->setRank($recipient, $package->give_rank);
            $this->rcon->disconnectUser($recipient);

            return;
        }

        $recipient->update(['rank' => $package->give_rank]);
    }

    private function sendCurrency(User $recipient, WebsiteShopArticle $package): void
    {
        $sendCurrency = app(SendCurrency::class);

        $sendCurrency->execute($recipient, 'credits', $package->credits);
        $sendCurrency->execute($recipient, 'duckets', $package->duckets);
        $sendCurrency->execute($recipient, 'diamonds', $package->diamonds);
    }

    private function sendFurniture(User $recipient, string $furnitureJson): void
    {
        $sendFurniture = app(SendFurniture::class);

        $sendFurniture->execute($recipient, json_decode($furnitureJson, true));
    }

    private function isGiftAttempt(User $buyer, User $recipient): bool
    {
        return $buyer->id !== $recipient->id;
    }
}
