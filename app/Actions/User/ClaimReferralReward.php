<?php

namespace App\Actions\User;

use App\Actions\SendCurrency;
use App\Enums\CurrencyTypes;
use App\Models\User;
use App\Models\User\UserReferral;
use Illuminate\Support\Facades\DB;

class ClaimReferralReward
{
    public function __construct(private readonly SendCurrency $sendCurrency) {}

    public function execute(User $user, string $ipAddress): bool
    {
        $requiredReferrals = (int) setting('referrals_needed');
        $rewardAmount = (int) setting('referral_reward_amount');

        if ($requiredReferrals < 1 || $rewardAmount < 1) {
            return false;
        }

        return DB::transaction(function () use ($user, $ipAddress, $requiredReferrals, $rewardAmount): bool {
            $referrals = UserReferral::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($referrals === null || $referrals->referrals_total < $requiredReferrals) {
                return false;
            }

            $referrals->decrement('referrals_total', $requiredReferrals);
            $this->sendCurrency->execute($user, CurrencyTypes::Diamonds, $rewardAmount);
            $user->claimedReferralLog()->create([
                'ip_address' => $ipAddress,
            ]);

            return true;
        }, attempts: 3);
    }
}
