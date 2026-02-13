<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\WebsiteShopArticle;
use App\Models\Shop\WebsiteShopCategory;
use App\Models\User;
use App\Services\Shop\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends Controller
{
    public function __construct(
        private readonly PurchaseService $purchaseService,
    ) {}

    public function __invoke(?WebsiteShopCategory $category)
    {
        $packages = $this->getPackages($category);

        return view('shop.shop', [
            'articles' => $packages->with(['rank:id,rank_name', 'features'])->get(),
            'categories' => WebsiteShopCategory::whereHas('articles')->get(),
        ]);
    }

    public function purchase(WebsiteShopArticle $package, Request $request): Response
    {
        $buyer = Auth::user();
        $recipient = $this->resolveRecipient($request, $package);

        if (! $recipient) {
            return to_route('shop.index')->withErrors(['message' => __('Recipient not found')]);
        }

        $validation = $this->purchaseService->validatePurchase($package, $buyer, $recipient);

        if (! $validation['valid']) {
            return to_route('shop.index')->withErrors(['message' => $validation['message']]);
        }

        $this->purchaseService->deductBalance($buyer, $package->price());
        $this->purchaseService->processPackage($package, $recipient, $buyer);

        return to_route('shop.index')->with(
            'success',
            $this->purchaseService->getSuccessMessage($package, $buyer, $recipient),
        );
    }

    private function getPackages(?WebsiteShopCategory $category)
    {
        if ($category && $category->exists) {
            return $category->articles()->orderBy('position');
        }

        return WebsiteShopArticle::orderBy('position');
    }

    private function resolveRecipient(Request $request, WebsiteShopArticle $package): ?User
    {
        if (! $request->has('receiver')) {
            return Auth::user();
        }

        return User::where('username', $request->input('receiver'))->first();
    }
}
