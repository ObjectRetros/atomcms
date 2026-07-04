<?php

namespace App\Actions\Shop;

use App\Actions\SendBadges;
use App\Actions\SendCurrency;
use App\Actions\SendFurniture;
use App\Contracts\Rcon;
use App\Enums\CurrencyTypes;
use App\Exceptions\ShopPurchaseException;
use App\Models\Shop\WebsiteShopArticle;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use JsonException;

class PurchasePackage
{
    public function __construct(
        private readonly Rcon $rcon,
        private readonly SendCurrency $sendCurrency,
        private readonly SendFurniture $sendFurniture,
        private readonly SendBadges $sendBadges,
    ) {}

    /**
     * Run a purchase end to end, returning the success message.
     *
     * @throws ShopPurchaseException when the purchase cannot proceed
     */
    public function execute(User $buyer, WebsiteShopArticle $package, ?string $receiver): string
    {
        $recipient = $this->resolveRecipient($buyer, $package, $receiver);

        $this->ensurePurchasable($buyer, $recipient, $package);

        $this->fulfil($buyer, $recipient, $package, $this->decodeFurniture($package));

        return $this->successMessage($buyer, $recipient, $package);
    }

    private function resolveRecipient(User $buyer, WebsiteShopArticle $package, ?string $receiver): User
    {
        if (blank($receiver)) {
            return $buyer;
        }

        if (! $package->is_giftable) {
            throw new ShopPurchaseException(__('This package is not giftable'));
        }

        return User::where('username', $receiver)->first()
            ?? throw new ShopPurchaseException(__('Recipient not found'));
    }

    private function ensurePurchasable(User $buyer, User $recipient, WebsiteShopArticle $package): void
    {
        if ($package->give_rank && $recipient->rank >= $package->give_rank) {
            throw new ShopPurchaseException($recipient->is($buyer)
                ? __('You are already this or a higher rank')
                : __('The recipient is already this or a higher rank'));
        }

        if (! $this->rcon->isConnected() && $recipient->online) {
            throw new ShopPurchaseException(__('Please logout before purchasing a package'));
        }

        if ($buyer->website_balance < $package->price()) {
            throw new ShopPurchaseException($this->insufficientMessage($buyer, $package->price()));
        }
    }

    /**
     * @return array<int, array{item_id: int, amount: int}>
     */
    private function decodeFurniture(WebsiteShopArticle $package): array
    {
        if (! $package->furniture) {
            return [];
        }

        try {
            return json_decode($package->furniture, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new ShopPurchaseException(__('This package is currently unavailable'));
        }
    }

    /**
     * @param  array<int, array{item_id: int, amount: int}>  $furniture
     */
    private function fulfil(User $buyer, User $recipient, WebsiteShopArticle $package, array $furniture): void
    {
        $price = $package->price();

        $delivered = DB::transaction(function () use ($buyer, $recipient, $package, $price, $furniture): bool {
            $locked = $this->lockUsers($buyer, $recipient);
            $lockedBuyer = $locked->get($buyer->id);
            $lockedRecipient = $locked->get($recipient->id);

            if (! $lockedBuyer || ! $lockedRecipient || $lockedBuyer->website_balance < $price) {
                return false;
            }

            $lockedBuyer->decrement('website_balance', $price);
            $this->deliver($lockedRecipient, $package, $furniture);

            return true;
        });

        if (! $delivered) {
            throw new ShopPurchaseException($this->insufficientMessage($buyer->fresh(), $price));
        }
    }

    /**
     * @param  array<int, array{item_id: int, amount: int}>  $furniture
     */
    private function deliver(User $recipient, WebsiteShopArticle $package, array $furniture): void
    {
        $this->sendCurrency->execute($recipient, CurrencyTypes::Credits, (int) $package->credits);
        $this->sendCurrency->execute($recipient, CurrencyTypes::Duckets, (int) $package->duckets);
        $this->sendCurrency->execute($recipient, CurrencyTypes::Diamonds, (int) $package->diamonds);

        if ($package->give_rank) {
            $this->grantRank($recipient, $package->give_rank);
        }

        if ($package->badges) {
            $this->sendBadges->execute($recipient, $package->badges);
        }

        if ($furniture) {
            $this->sendFurniture->execute($recipient, $furniture);
        }
    }

    private function grantRank(User $recipient, int $rank): void
    {
        if (! $this->rcon->isConnected()) {
            $recipient->update(['rank' => $rank]);

            return;
        }

        $this->rcon->setRank($recipient, $rank);
        $this->rcon->disconnectUser($recipient);
    }

    /**
     * @return Collection<int, User>
     */
    private function lockUsers(User $buyer, User $recipient): Collection
    {
        return User::whereKey([$buyer->id, $recipient->id])
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    private function insufficientMessage(User $buyer, int $price): string
    {
        return __('You need to top-up your account with another $:amount to purchase this package', [
            'amount' => $price - $buyer->website_balance,
        ]);
    }

    private function successMessage(User $buyer, User $recipient, WebsiteShopArticle $package): string
    {
        if ($recipient->is($buyer)) {
            return __('You have successfully purchased the package :name', ['name' => $package->name]);
        }

        return __('You have successfully purchased the package :name for :username', [
            'name' => $package->name,
            'username' => $recipient->username,
        ]);
    }
}
