<?php

namespace App\Http\Controllers;

use App\Exceptions\ConnectionRefusedException;
use App\Http\Requests\RedeemVoucherRequest;
use App\Models\User;
use App\Models\WebsiteShopUsedVoucher;
use App\Models\WebsiteShopVoucher;
use App\Models\WebsiteShopProduct;
use App\Services\RconService;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    private RconService $rcon;

    public function __construct(RconService $rconService)
    {
        $this->rcon = $rconService;
    }

    public function __invoke()
    {
        return view('shop.shop', [
            'packages' => WebsiteShopProduct::query()
                ->orderBy('order')
                ->with('features')
                ->get(),
        ]);
    }

    public function redeemVoucher(RedeemVoucherRequest $request)
    {
        $voucher = WebsiteShopVoucher::where('code', '=', $request->input('code'))
            ->withCount('usedVouchers')
            ->first();

        // Check whether the voucher has expired or reached its max uses
        if (!is_null($voucher->expire_at) && (now()->greaterThan($voucher->expire_at)) || $voucher->used_vouchers_count >= $voucher->max_uses) {
            return redirect()->back()->withErrors([
                'message' => __('It seems like the voucher is no longer valid'),
            ]);
        }

        // Checks whether the user has redeemed the voucher already
        if ($request->user()->vouchersRedeemed()->where('code_id', '=', $voucher->id)->exists() || $voucher->usedVouchers()->where('ip_address', '=', $request->ip())->exists()) {
            return redirect()->back()->withErrors([
                'message' => __('It seems like you have already redeemed this voucher once'),
            ]);
        }

        $request->user()->vouchersRedeemed()->create([
            'code_id' => $voucher->id,
            'ip_address' => $request->ip(),
        ]);

        $request->user()->increment('website_store_balance', $voucher->amount);

        return redirect()->back()->with(
            'success',
            __(':amount$ has been added to your store balance!', ['amount' => $voucher->amount])
        );
    }

    public function purchase(WebsiteShopProduct $product, RconService $rconService)
    {
        $productData = json_decode($product->data,true);
        $package = $productData['content'];
        $user = Auth::user();

        // Prevent a user from buying a VIP package is they're already the same or above the vip rank
        if ($product->type === 'vip' && $user->rank >= $package['rank']) {
            return redirect()->back()->withErrors([
                'message' => __('It seems like you already have this or a higher rank, please select another package if possible.')
            ]);
        }

        // Prevent user from buying a package in their funds is too low
        if ($user->website_store_balance < $product->price()) {
            return redirect()->back()->withErrors([
                'message' => __('You do not have enough balance, to purchase this package. Please add another $:amount before having enough', ['amount' => $product->price() - $user->website_store_balance])
            ]);
        }

        // Make sure RCON is accessible before allowing to purchase a package
        if (!$rconService->isConnected()) {
            return redirect()->back()->withErrors([
                'message' => __('It seems like there was an issue connecting to the RCON service, please try again later or contact the owners of the hotel'),
            ]);
        }

        match ($product->type) {
            'vip' => $this->giftVipPackage($user, $package),
            'furniture' => $this->giftFurniturePackage($user, $package),
        };

        $user->decrement('website_store_balance', $product->price());

        return redirect()->back()->with('success', __('Thank you for purchasing :package', ['package' => $productData['name'] ?? 'VIP']));
   }

    private function giftVipPackage(User $user, $package)
    {
        if (!array_key_exists('rank', $package) || empty($package['rank'])) {
            return redirect()->back()->withErrors([
                'message' => __('The package does not seem to contain any VIP rank, please contact one of the hotel owners'),
            ]);
        }

        $this->rcon->setRank($user, $package['rank']);

        if (array_key_exists('credits', $package['currencies']) && $package['currencies']['credits'] > 0) {
            $this->rcon->giveCredits($user, $package['currencies']['credits']);
        }

        if (!array_key_exists('types', $package['currencies']) && !empty($package['currencies']['types'])) {
            foreach ($package['currencies']['types'] as $type => $amount) {
                dd($type, $amount);
                $this->rcon->givePoints($user, (int)$type, (int)$amount);
            }
        }

        if (array_key_exists('badges', $package) && !empty($package['badges'])) {
            foreach ($package['badges'] as $badge) {
                $this->rcon->giveBadge($user, $badge);
            }
        }
    }

    private function giftFurniturePackage(User $user, $package)
    {
        // TODO: Add logic
        // Furniture pack example (just for testing)
//        $furniturePackData = [
//            'data' => json_encode([
//                'content' => [
//                    [
//                        'item_id' => 202,
//                        'quantity' => 15,
//                    ],
//                    [
//                        'item_id' => 250,
//                        'quantity' => 20,
//                    ],
//                ],
//                'price' => 15,
//            ]),
//            'type' => 'furniture',
//        ];
//        $package = json_decode($furniturePackData['data'], true);
//
//        $count = 0;
//
//        foreach ($package['content'] as $item) {
//            for ($i = 0; $i < $item['quantity']; $i++) {
//                $count++;
//
//                // Send sendGift RCON here
//            }
//
//            dd($count, $item['item_id']);
//        }
    }
}