<?php

namespace App\Http\Controllers\Shop;

use App\Actions\Shop\PurchasePackage;
use App\Exceptions\ShopPurchaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ShopPurchaseRequest;
use App\Models\Shop\WebsiteShopArticle;
use App\Models\Shop\WebsiteShopCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __invoke(?WebsiteShopCategory $category): View
    {
        $packages = $category?->exists
            ? $category->articles()->orderBy('position')
            : WebsiteShopArticle::orderBy('position');

        return view('shop.shop', [
            'articles' => $packages->with(['rank:id,rank_name', 'features'])->get(),
            'categories' => WebsiteShopCategory::whereHas('articles')->get(),
        ]);
    }

    public function purchase(WebsiteShopArticle $package, ShopPurchaseRequest $request, PurchasePackage $purchasePackage): RedirectResponse
    {
        try {
            $message = $purchasePackage->execute($request->user(), $package, $request->input('receiver'));
        } catch (ShopPurchaseException $exception) {
            return to_route('shop.index')->withErrors(['message' => $exception->getMessage()]);
        }

        return to_route('shop.index')->with('success', $message);
    }
}
