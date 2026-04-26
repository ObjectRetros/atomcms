<?php

namespace App\Http\Controllers\Shop;

use App\Actions\SendCurrency;
use App\Actions\SendFurniture;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ShopPurchaseRequest;
use App\Models\Shop\WebsiteShopArticle;
use App\Models\Shop\WebsiteShopCategory;
use App\Models\User;
use App\Services\RconService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends Controller
{
    private RconService $rconService;

    public function __construct(RconService $rconService)
    {
        $this->rconService = $rconService;
    }

    public function __invoke(?WebsiteShopCategory $category)
    {
        $packages = WebsiteShopArticle::orderBy('position');

        if ($category && $category->exists) {
            $packages = $category->articles()->orderBy('position');
        }

        return view('shop.shop', [
            'articles' => $packages->with(['rank:id,rank_name', 'features'])->get(),
            'categories' => WebsiteShopCategory::whereHas('articles')->get(),
        ]);
    }

    private function giveBadges(User $user, string $badges)
    {
        $badgeList = array_filter(array_map('trim', explode(';', $badges)));
        $ownedBadges = $user->badges()->pluck('badge_code')->toArray();

        foreach ($badgeList as $badge) {
            if (in_array($badge, $ownedBadges, true)) {
                continue;
            }

            if ($this->rconService->isConnected) {
                $this->rconService->giveBadge($user, $badge);

                continue;
            }

            $user->badges()->updateOrCreate([
                'user_id' => $user->id,
                'badge_code' => $badge,
            ]);
        }
    }

    public function purchase(WebsiteShopArticle $package, ShopPurchaseRequest $request, SendCurrency $sendCurrency, SendFurniture $sendFurniture): Response
    {
        $buyer = $request->user();
        $recipient = $buyer;

        if ($request->filled('receiver')) {
            if (! $package->is_giftable) {
                return to_route('shop.index')->withErrors(
                    ['message' => __('This package is not giftable')],
                );
            }

            $recipient = User::where('username', $request->input('receiver'))->first();

            if (! $recipient) {
                return to_route('shop.index')->withErrors(
                    ['message' => __('Recipient not found')],
                );
            }

        }

        if ($package->give_rank && $recipient->rank >= $package->give_rank) {
            $message = __('You are already this or a higher rank');

            if ($recipient->isNot($buyer)) {
                $message = __('The recipient is already this or a higher rank');
            }

            return to_route('shop.index')->withErrors(
                ['message' => $message],
            );
        }

        if (! $this->rconService->isConnected && $recipient->online) {
            return to_route('shop.index')->withErrors(
                ['message' => __('Please logout before purchasing a package')],
            );
        }

        try {
            $furniture = $package->furniture
                ? json_decode($package->furniture, true, 512, JSON_THROW_ON_ERROR)
                : [];
        } catch (JsonException) {
            return to_route('shop.index')->withErrors(
                ['message' => __('This package is currently unavailable')],
            );
        }

        $price = $package->price();

        if ($buyer->website_balance < $price) {
            return to_route('shop.index')->withErrors(
                ['message' => __('You need to top-up your account with another $:amount to purchase this package', ['amount' => ($price - $buyer->website_balance)])],
            );
        }

        $users = DB::transaction(function () use ($buyer, $recipient, $package, $price, $sendCurrency, $sendFurniture, $furniture) {
            $users = $this->lockPurchaseUsers($buyer, $recipient);
            $buyer = $users->get($buyer->id);
            $recipient = $users->get($recipient->id);

            if (! $buyer || ! $recipient || $buyer->website_balance < $price) {
                return;
            }

            $buyer->decrement('website_balance', $price);

            $sendCurrency->execute($recipient, 'credits', $package->credits);
            $sendCurrency->execute($recipient, 'duckets', $package->duckets);
            $sendCurrency->execute($recipient, 'diamonds', $package->diamonds);

            if ($package->give_rank) {
                if ($this->rconService->isConnected) {
                    $this->rconService->setRank($recipient, $package->give_rank);
                    $this->rconService->disconnectUser($recipient);
                } else {
                    $recipient->update([
                        'rank' => $package->give_rank,
                    ]);
                }
            }

            if ($package->badges) {
                $this->giveBadges($recipient, $package->badges);
            }

            if ($furniture) {
                $sendFurniture->execute($recipient, $furniture);
            }

            return $users;
        });

        if (! $users) {
            return to_route('shop.index')->withErrors(
                ['message' => __('You need to top-up your account with another $:amount to purchase this package', ['amount' => ($price - $buyer->fresh()->website_balance)])],
            );
        }

        $message = __('You have successfully purchased the package :name', ['name' => $package->name]);

        if ($recipient->isNot($buyer)) {
            $message = __('You have successfully purchased the package :name for :username', ['name' => $package->name, 'username' => $recipient->username]);
        }

        return to_route('shop.index')->with('success', $message);
    }

    private function lockPurchaseUsers(User $buyer, User $recipient): Collection
    {
        return User::whereKey([$buyer->id, $recipient->id])
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }
}
