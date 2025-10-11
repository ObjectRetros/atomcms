<?php

namespace App\Http\Controllers\Shop;

use App\Actions\SendCurrency;
use App\Actions\SendFurniture;
use App\Http\Controllers\Controller;
use App\Models\Shop\WebsiteShopArticle;
use App\Models\Shop\WebsiteShopCategory;
use App\Models\User;
use App\Services\RconService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $badgeList = explode(';', $badges);
        $ownedBadges = $user->badges()->pluck('badge_code')->toArray();

        foreach ($badgeList as $badge) {
            if (in_array($badge, $ownedBadges)) {
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

    public function purchase(WebsiteShopArticle $package, Request $request, SendCurrency $sendCurrency): Response
    {
        $user = Auth::user();

        if ($request->has('receiver')) {
            if (! $package->is_giftable) {
                return to_route('shop.index')->withErrors(
                    ['message' => __('This package is not giftable')],
                );
            }

            $user = User::where('username', $request->input('receiver'))->first();

            if (! $user) {
                return to_route('shop.index')->withErrors(
                    ['message' => __('Recipient not found')],
                );
            }

        }

        if ($package->give_rank && $user->rank >= $package->give_rank) {
            $message = __('You are already this or a higher rank');

            if ($user->username !== Auth::user()->username) {
                $message = __('The recipient is already this or a higher rank');
            }

            return to_route('shop.index')->withErrors(
                ['message' => $message],
            );
        }

        if (! $this->rconService->isConnected && $user->online === '1') {
            return to_route('shop.index')->withErrors(
                ['message' => __('Please logout before purchasing a package')],
            );
        }

        if (Auth::user()->website_balance < $package->price()) {
            return to_route('shop.index')->withErrors(
                ['message' => __('You need to top-up your account with another $:amount to purchase this package', ['amount' => ($package->price() - Auth::user()->website_balance)])],
            );
        }

        Auth::user()?->decrement('website_balance', $package->price());

        $sendCurrency->execute($user, 'credits', $package->credits);
        $sendCurrency->execute($user, 'duckets', $package->duckets);
        $sendCurrency->execute($user, 'diamonds', $package->diamonds);

        if ($package->give_rank) {
            if ($this->rconService->isConnected) {
                $this->rconService->setRank($user, $package->give_rank);
                $this->rconService->disconnectUser($user);
            } else {
                $user->update([
                    'rank' => $package->give_rank,
                ]);
            }
        }

        if ($package->badges) {
            $this->giveBadges($user, $package->badges);
        }

        if ($package->furniture) {
            $this->handleFurniture(json_decode($package->furniture, true));
        }

        $message = __('You have successfully purchased the package :name', ['name' => $package->name]);

        if ($user->username !== Auth::user()->username) {
            $message = __('You have successfully purchased the package :name for :username', ['name' => $package->name, 'username' => $user->username]);
        }

        return to_route('shop.index')->with('success', $message);
    }

    public function handleFurniture(array $furniture)
    {
        $sendFurniture = app(SendFurniture::class);

        $sendFurniture->execute(Auth::user(), $furniture);
    }
}
