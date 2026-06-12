<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopVoucherFormRequest;
use App\Models\Shop\WebsiteShopVoucher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ShopVoucherController extends Controller
{
    public function __invoke(ShopVoucherFormRequest $request)
    {
        $user = $request->user();
        $voucher = WebsiteShopVoucher::where('code', $request->string('code'))->first();

        if (is_null($voucher) || ($voucher->expires_at && $voucher->expires_at->lte(now()))) {
            return $this->notFound();
        }

        return DB::transaction(fn () => $this->redeem($user, $voucher->id));
    }

    /**
     * Redeem under a row lock so concurrent requests cannot double-credit. The
     * unique (user_id, voucher_id) index is the authoritative single-use guard.
     */
    private function redeem(User $user, int $voucherId)
    {
        $voucher = WebsiteShopVoucher::whereKey($voucherId)->lockForUpdate()->first();

        if ($voucher->max_uses && $voucher->use_count >= $voucher->max_uses) {
            return $this->notFound();
        }

        $used = $user->usedShopVouchers()->firstOrCreate(['voucher_id' => $voucher->id]);

        if (! $used->wasRecentlyCreated) {
            return redirect()->back()->withErrors([
                'message' => __('You can only use each shop voucher once'),
            ]);
        }

        $user->increment('website_balance', $voucher->amount);
        $voucher->increment('use_count');

        if ($voucher->max_uses && $voucher->use_count >= $voucher->max_uses) {
            $voucher->update(['expires_at' => now()]);
        }

        return redirect()->back()->with('success', __('Your balance has been increased by $:amount', ['amount' => $voucher->amount]));
    }

    private function notFound()
    {
        return redirect()->back()->withErrors([
            'message' => __('No active voucher with the given code was found'),
        ]);
    }
}
