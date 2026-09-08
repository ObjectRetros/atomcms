<?php

namespace App\Actions\Shop;

use App\Models\Shop\WebsiteShopVoucher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RedeemVoucher
{
    public function execute(User $user, string $code): int
    {
        return DB::transaction(function () use ($user, $code): int {
            $voucher = WebsiteShopVoucher::where('code', $code)->lockForUpdate()->first();
            if ($voucher === null || ($voucher->expires_at && $voucher->expires_at->lte(now())) || ($voucher->max_uses && $voucher->use_count >= $voucher->max_uses)) {
                throw ValidationException::withMessages(['message' => __('No active voucher with the given code was found')]);
            }

            $used = $user->usedShopVouchers()->firstOrCreate(['voucher_id' => $voucher->id]);
            if (! $used->wasRecentlyCreated) {
                throw ValidationException::withMessages(['message' => __('You can only use each shop voucher once')]);
            }
            $user->increment('website_balance', $voucher->amount);
            $voucher->increment('use_count');
            if ($voucher->max_uses && $voucher->use_count >= $voucher->max_uses) {
                $voucher->update(['expires_at' => now()]);
            }

            return $voucher->amount;
        });
    }
}
