<?php

namespace App\Http\Controllers\User;

use App\Actions\SendCurrency;
use App\Enums\CurrencyTypes;
use App\Http\Controllers\Controller;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;

class ReferralController extends Controller
{
    public function __invoke(SendCurrency $sendCurrency): RedirectResponse
    {
        $user = AuthenticatedUser::current();
        $referralsNeeded = (int) setting('referrals_needed', 5);

        if (! $user->referrals || $user->referrals->referrals_total < $referralsNeeded) {
            return redirect()->back()->withErrors([
                'message' => __('You do not have enough referrals to claim your reward'),
            ]);
        }

        // Decrease the total amount of referrals with the amount needed to claim reward
        $user->referrals->decrement('referrals_total', $referralsNeeded);

        $sendCurrency->execute($user, CurrencyTypes::Diamonds, (int) setting('referral_reward_amount', 30));

        // Log the claim
        $user->claimedReferralLog()->create([
            'ip_address' => request()->ip(),
        ]);

        return redirect()->back()->with('success', __('Woah! You have successfully claimed your reward - Keep up the good work!'));
    }
}
