<?php

namespace App\Models\Traits;

use App\Models\User\ClaimedReferralLog;
use App\Models\User\Referral;
use App\Models\User\UserReferral;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasReferrals
{
    public function referrals(): HasOne
    {
        return $this->hasOne(UserReferral::class);
    }

    public function userReferrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function claimedReferralLog(): HasMany
    {
        return $this->hasMany(ClaimedReferralLog::class);
    }

    public function referralsNeeded(): int
    {
        $referrals = $this->referrals?->referrals_total ?? 0;

        return setting('referrals_needed') - $referrals;
    }
}
