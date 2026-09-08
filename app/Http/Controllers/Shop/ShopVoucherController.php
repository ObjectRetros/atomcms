<?php

namespace App\Http\Controllers\Shop;

use App\Actions\Shop\RedeemVoucher;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShopVoucherFormRequest;
use App\Support\AuthenticatedUser;
use App\Support\StorefrontMoney;
use Illuminate\Http\RedirectResponse;

class ShopVoucherController extends Controller
{
    public function __invoke(ShopVoucherFormRequest $request, RedeemVoucher $vouchers): RedirectResponse
    {
        $amount = $vouchers->execute(AuthenticatedUser::from($request), $request->string('code')->toString());

        return redirect()->back()->with('success', __('Your balance has been increased by :amount', ['amount' => StorefrontMoney::format($amount)]));
    }
}
