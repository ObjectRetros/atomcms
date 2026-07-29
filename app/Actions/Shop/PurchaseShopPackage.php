<?php

namespace App\Actions\Shop;

use App\Contracts\Rcon;
use App\Exceptions\RconConnectionException;
use App\Exceptions\ShopPurchaseException;
use App\Models\Shop\WebsiteShopPackage;
use App\Models\Shop\WebsiteShopPurchase;
use App\Models\User;
use App\Support\StorefrontMoney;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final readonly class PurchaseShopPackage
{
    public function __construct(
        private readonly Rcon $rcon,
        private readonly FulfillPackage $fulfillPackage,
    ) {}

    /**
     * Run an item-package purchase end to end, returning the success message.
     *
     * @throws ShopPurchaseException when the purchase cannot proceed
     */
    public function execute(User $buyer, WebsiteShopPackage $package, ?string $receiver): string
    {
        $recipient = $this->resolveRecipient($buyer, $package, $receiver);

        $this->ensurePurchasable($buyer, $recipient, $package);
        $this->prepareRecipient($recipient);

        $this->fulfil($buyer, $recipient, $package);

        return $this->successMessage($buyer, $recipient, $package);
    }

    private function resolveRecipient(User $buyer, WebsiteShopPackage $package, ?string $receiver): User
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

    private function ensurePurchasable(User $buyer, User $recipient, WebsiteShopPackage $package): void
    {
        $this->ensurePackageEligibility($recipient, $package, $buyer);

        $this->ensureWithinPurchaseLimit($buyer, $package);

        if ($buyer->website_balance < $package->price) {
            throw new ShopPurchaseException($this->insufficientMessage($buyer, $package->price));
        }
    }

    private function ensurePackageEligibility(User $recipient, WebsiteShopPackage $package, User $buyer): void
    {
        if (! $package->isAvailable()) {
            throw new ShopPurchaseException(__('This package is no longer available'));
        }

        if ($package->min_rank && $recipient->rank < $package->min_rank) {
            throw new ShopPurchaseException($recipient->is($buyer)
                ? __('Your rank is too low to purchase this package')
                : __("The recipient's rank is too low to receive this package"));
        }

        if ($package->max_rank && $recipient->rank > $package->max_rank) {
            throw new ShopPurchaseException($recipient->is($buyer)
                ? __('Your rank is too high to purchase this package')
                : __("The recipient's rank is too high to receive this package"));
        }
    }

    private function prepareRecipient(User $recipient): void
    {
        if (! $recipient->online) {
            return;
        }

        try {
            // Arcturus currently reports HABBO_NOT_FOUND even after delivering
            // alertuser, so this response is intentionally best-effort.
            $this->rcon->sendCommand('alertuser', [
                'user_id' => $recipient->id,
                'message' => __('You are being disconnected so your package can be delivered safely. Please wait for the purchase to finish before reconnecting.'),
            ]);

            $this->rcon->sendCommand('disconnect', [
                'user_id' => $recipient->id,
                'username' => $recipient->username,
            ]);
        } catch (RconConnectionException) {
            throw new ShopPurchaseException(__('Please logout before purchasing a package'));
        }

        if ($recipient->refresh()->online) {
            throw new ShopPurchaseException(__('Please logout before purchasing a package'));
        }
    }

    /**
     * Deliver, decrement stock and charge in one transaction. The buyer,
     * recipient and package locks serialize balance, limit and stock checks.
     */
    private function fulfil(User $buyer, User $recipient, WebsiteShopPackage $package): void
    {
        DB::transaction(function () use ($buyer, $recipient, $package): void {
            $locked = $this->lockUsers($buyer, $recipient);
            $lockedBuyer = $locked->get($buyer->id);
            $lockedRecipient = $locked->get($recipient->id);
            $lockedPackage = WebsiteShopPackage::whereKey($package->id)->lockForUpdate()->first();

            if (! $lockedBuyer || ! $lockedRecipient) {
                throw new ShopPurchaseException(__('The buyer or recipient is no longer available'));
            }

            if (! $lockedPackage) {
                throw new ShopPurchaseException(__('This package is no longer available'));
            }

            $price = $lockedPackage->price;

            if ($lockedBuyer->website_balance < $price) {
                throw new ShopPurchaseException($this->insufficientMessage($lockedBuyer, $price));
            }

            if ($lockedRecipient->online) {
                throw new ShopPurchaseException(__('Please logout before purchasing a package'));
            }

            $this->ensurePackageEligibility($lockedRecipient, $lockedPackage, $lockedBuyer);
            $this->ensureWithinPurchaseLimit($lockedBuyer, $lockedPackage);

            $lockedPackage->load('items');
            $this->fulfillPackage->execute($lockedRecipient, $lockedPackage);

            $this->takeStock($lockedPackage);

            $lockedBuyer->decrement('website_balance', $price);

            WebsiteShopPurchase::create([
                'user_id' => $lockedBuyer->id,
                'website_shop_package_id' => $lockedPackage->id,
                'gifted_to' => $lockedRecipient->is($lockedBuyer) ? null : $lockedRecipient->id,
            ]);
        });
    }

    private function ensureWithinPurchaseLimit(User $buyer, WebsiteShopPackage $package): void
    {
        if (! $package->limit_per_user) {
            return;
        }

        $purchases = WebsiteShopPurchase::where('user_id', $buyer->id)
            ->where('website_shop_package_id', $package->id)
            ->count();

        if ($purchases >= $package->limit_per_user) {
            throw new ShopPurchaseException(__('You have already purchased this package the maximum number of times (:limit)', [
                'limit' => $package->limit_per_user,
            ]));
        }
    }

    /**
     * Guarded decrement so the last unit can only be sold once.
     */
    private function takeStock(WebsiteShopPackage $package): void
    {
        if ($package->stock === null) {
            return;
        }

        if ($package->stock <= 0) {
            throw new ShopPurchaseException(__('This package is out of stock'));
        }

        $package->decrement('stock');
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
        return __('You need to top-up your account with another :amount to purchase this package', [
            'amount' => StorefrontMoney::format($price - $buyer->website_balance),
        ]);
    }

    private function successMessage(User $buyer, User $recipient, WebsiteShopPackage $package): string
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
