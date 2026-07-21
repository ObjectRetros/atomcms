<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopVoucherFormRequest;
use App\Models\Shop\WebsiteShopVoucher;
use App\Models\User;
use App\Support\AuthenticatedUser;
use App\Support\StorefrontMoney;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ShopVoucherController extends Controller
{
    public function __invoke(ShopVoucherFormRequest $request): RedirectResponse
    {
        $user = AuthenticatedUser::from($request);
        $voucher = WebsiteShopVoucher::where('code', $request->string('code'))->first();

        if ($voucher === null || ! $this->isActive($voucher)) {
            return $this->notFound();
        }

        return DB::transaction(fn (): RedirectResponse => $this->redeem($user, $voucher->id));
    }

    /**
     * Redeem under a row lock so concurrent requests cannot double-credit. The
     * unique (user_id, voucher_id) index is the authoritative single-use guard.
     */
    private function redeem(User $user, int $voucherId): RedirectResponse
    {
        $voucher = WebsiteShopVoucher::whereKey($voucherId)->lockForUpdate()->first();

        // Re-check under the lock: a concurrent redemption may have exhausted
        // or expired the voucher since the pre-flight read.
        if ($voucher === null || ! $this->isActive($voucher)) {
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

        return redirect()->back()->with('success', __('Your balance has been increased by :amount', [
            'amount' => StorefrontMoney::format($voucher->amount),
        ]));
    }

    private function isActive(WebsiteShopVoucher $voucher): bool
    {
        $expired = $voucher->expires_at && $voucher->expires_at->lte(now());
        $exhausted = $voucher->max_uses && $voucher->use_count >= $voucher->max_uses;

        return ! $expired && ! $exhausted;
    }

    private function notFound(): RedirectResponse
    {
        return redirect()->back()->withErrors([
            'message' => __('No active voucher with the given code was found'),
        ]);
    }
}
