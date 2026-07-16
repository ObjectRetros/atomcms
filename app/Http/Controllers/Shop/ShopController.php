<?php

namespace App\Http\Controllers\Shop;

use App\Actions\Shop\PurchaseShopPackage;
use App\Exceptions\ShopPurchaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\PurchasePackageRequest;
use App\Models\Shop\WebsiteShopCategory;
use App\Models\Shop\WebsiteShopPackage;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __invoke(?WebsiteShopCategory $category): View
    {
        $packages = $category?->exists
            ? $category->packages()->orderBy('sort_order')
            : WebsiteShopPackage::orderBy('sort_order');

        return view('shop.shop', [
            'shopPackages' => $packages->with('items')->get(),
            'categories' => WebsiteShopCategory::where('is_active', true)
                ->whereHas('packages')
                ->get(),
        ]);
    }

    public function purchasePackage(WebsiteShopPackage $package, PurchasePackageRequest $request, PurchaseShopPackage $purchaseShopPackage): RedirectResponse
    {
        try {
            $message = $purchaseShopPackage->execute(AuthenticatedUser::from($request), $package, $request->input('receiver'));
        } catch (ShopPurchaseException $exception) {
            return to_route('shop.index')->withErrors(['message' => $exception->getMessage()]);
        }

        return to_route('shop.index')->with('success', $message);
    }
}
