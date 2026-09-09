<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Shop\PurchaseShopPackage;
use App\Actions\Shop\RedeemVoucher;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountTopupFormRequest;
use App\Http\Requests\Shop\PurchasePackageRequest;
use App\Http\Requests\ShopVoucherFormRequest;
use App\Models\Shop\WebsiteShopCategory;
use App\Models\Shop\WebsiteShopPackage;
use App\Models\Shop\WebsiteShopPurchase;
use App\Services\Payments\PaypalOrderCreator;
use App\Services\Shop\IdempotentOperation;
use App\Services\Shop\ShopReadService;
use App\Support\AuthenticatedUser;
use App\Support\StorefrontMoney;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopController extends Controller
{
    public function index(Request $request, ShopReadService $shop): JsonResponse
    {
        $request->validate(['category' => ['sometimes', 'string', 'max:255']]);
        $category = $request->filled('category') ? WebsiteShopCategory::where('slug', $request->input('category'))->firstOrFail() : null;
        $catalog = $shop->catalog($category);

        return response()->json(['data' => [
            'categories' => $catalog['categories']->map(fn ($category): array => ['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug, 'icon' => $category->icon]),
            'packages' => $catalog['shopPackages']->map(fn ($package): array => [
                'id' => $package->id, 'name' => $package->name, 'description' => $package->description, 'image' => $package->image ? asset('storage/' . $package->image) : null,
                'price' => ['amount_minor' => $package->price, 'currency' => StorefrontMoney::currencyCode()],
                'stock' => $package->stock, 'is_giftable' => $package->is_giftable, 'available' => $package->isAvailable(),
                'min_rank' => $package->min_rank, 'max_rank' => $package->max_rank, 'limit_per_user' => $package->limit_per_user,
                'items' => $package->items->map(fn ($item): array => ['id' => $item->id, 'name' => $item->name, 'image' => $item->image ? asset('storage/' . $item->image) : null, 'quantity' => $item->pivot?->quantity]),
            ]),
        ]]);
    }

    public function purchase(WebsiteShopPackage $package, PurchasePackageRequest $request, PurchaseShopPackage $purchase, IdempotentOperation $idempotency): JsonResponse
    {
        $actor = AuthenticatedUser::from($request);
        $receiver = $request->input('receiver');
        $result = $idempotency->execute($actor, 'package-purchase', $request->header('Idempotency-Key'), ['package_id' => $package->id, 'receiver' => $receiver], function () use ($actor, $package, $receiver, $purchase): array {
            $receipt = $purchase->execute($actor, $package, $receiver);

            return ['id' => $receipt->purchaseId, 'package_name' => $receipt->packageName, 'recipient_username' => $receipt->recipientUsername, 'charged' => ['amount_minor' => $receipt->chargedMinor, 'currency' => $receipt->currency]];
        });

        return response()->json(['data' => $result], 201);
    }

    public function purchases(Request $request): AnonymousResourceCollection
    {
        $purchases = WebsiteShopPurchase::where('user_id', AuthenticatedUser::from($request)->id)->with(['package', 'giftedTo:id,username'])->latest('id')->paginate(20);

        return JsonResource::collection($purchases->through(fn ($purchase): array => ['id' => $purchase->id, 'package_id' => $purchase->website_shop_package_id, 'package_name' => $purchase->package?->name, 'recipient_username' => $purchase->giftedTo?->username, 'created_at' => $purchase->created_at?->toIso8601String()]));
    }

    public function voucher(ShopVoucherFormRequest $request, RedeemVoucher $vouchers): JsonResponse
    {
        $amount = $vouchers->execute(AuthenticatedUser::from($request), $request->string('code')->toString());

        return response()->json(['data' => ['credited' => ['amount_minor' => $amount, 'currency' => StorefrontMoney::currencyCode()]]]);
    }

    public function createOrder(AccountTopupFormRequest $request, PaypalOrderCreator $orders, IdempotentOperation $idempotency): JsonResponse
    {
        $actor = AuthenticatedUser::from($request);
        $result = $idempotency->execute($actor, 'paypal-order', $request->header('Idempotency-Key'), ['amount' => $request->integer('amount')], function (string $key) use ($actor, $request, $orders): array {
            $order = $orders->createWithReceipt($actor, $request->integer('amount'), $key);

            return ['id' => $order->orderId, 'approval_url' => $order->approvalUrl, 'amount_minor' => $order->amount, 'currency' => $order->currency];
        });

        return response()->json(['data' => $result], 201);
    }

    public function order(string $order, Request $request): JsonResponse
    {
        $transaction = AuthenticatedUser::from($request)->transactions()->where('transaction_id', $order)->firstOrFail();

        return response()->json(['data' => ['id' => $transaction->transaction_id, 'status' => $transaction->status?->value, 'amount_minor' => $transaction->amount, 'currency' => $transaction->currency, 'credited_at' => $transaction->credited_at?->toIso8601String()]]);
    }
}
