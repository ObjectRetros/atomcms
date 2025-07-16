<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopVoucherFormRequest;
use App\Models\Shop\WebsiteShopVoucher;

class ShopVoucherController extends Controller
{
    public function __invoke(ShopVoucherFormRequest $request)
    {
        $user = $request->user();
        $voucher = WebsiteShopVoucher::where('code', $request->string('code'))->first();

        if (is_null($voucher) || ($voucher->expires_at && $voucher->expires_at->lte(now()))) {
            return redirect()->back()->withErrors([
                'message' => __('No active voucher with the given code was found'),
            ]);
        }

        if ($user->usedShopVouchers()->where('voucher_id', $voucher->id)->exists()) {
            return redirect()->back()->withErrors([
                'message' => __('You can only use each shop voucher once'),
            ]);
        }

        $user->usedShopVouchers()->create([
            'voucher_id' => $voucher->id,
        ]);

        $user->increment('website_balance', $voucher->amount);

        $voucher->increment('use_count');

        if ($voucher->max_uses && $voucher->use_count >= $voucher->max_uses) {
            $voucher->update([
                'expires_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', __('Your balance has been increased by $:amount', ['amount' => $voucher->amount]));
    }
}