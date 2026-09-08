<?php

namespace App\Http\Controllers\Shop;

use App\Actions\Shop\PurchaseShopPackage;
use App\Exceptions\ShopPurchaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\PurchasePackageRequest;
use App\Models\Shop\WebsiteShopCategory;
use App\Models\Shop\WebsiteShopPackage;
use App\Services\Shop\ShopReadService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __invoke(?WebsiteShopCategory $category): View
    {
        return view('shop.shop', app(ShopReadService::class)->catalog($category));
    }

    public function purchasePackage(WebsiteShopPackage $package, PurchasePackageRequest $request, PurchaseShopPackage $purchaseShopPackage): RedirectResponse
    {
        $buyer = AuthenticatedUser::from($request);

        try {
            $receipt = $purchaseShopPackage->execute($buyer, $package, $request->input('receiver'));
        } catch (ShopPurchaseException $exception) {
            return to_route('shop.index')->withErrors(['message' => $exception->getMessage()]);
        }

        $message = $receipt->recipientUsername === $buyer->username
            ? __('You have successfully purchased the package :name', ['name' => $receipt->packageName])
            : __('You have successfully purchased the package :name for :username', ['name' => $receipt->packageName, 'username' => $receipt->recipientUsername]);

        return to_route('shop.index')->with('success', $message);
    }
}
