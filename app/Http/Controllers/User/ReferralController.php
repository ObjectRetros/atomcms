<?php

namespace App\Http\Controllers\User;

use App\Actions\User\ClaimReferralReward;
use App\Http\Controllers\Controller;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function __invoke(Request $request, ClaimReferralReward $claim): RedirectResponse
    {
        $claimed = $claim->execute(
            AuthenticatedUser::from($request),
            $request->ip() ?: 'unknown',
        );

        if (! $claimed) {
            return redirect()->back()->withErrors([
                'message' => __('You do not have enough referrals to claim your reward'),
            ]);
        }

        return redirect()->back()->with('success', __('Woah! You have successfully claimed your reward - Keep up the good work!'));
    }
}
