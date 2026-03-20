<?php

namespace App\Observers;

use App\Actions\Home\CreateDefaultHome;
use App\Models\Game\Player\UserCurrency;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        $user->settings()->create([
            'last_hc_payday' => (setting('give_hc_on_register') ?: '0') == '1' ? now()->addYears(10)->unix() : 0,
        ]);

        if ((setting('give_hc_on_register') ?: '0') == '1') {
            $user->hcSubscription()->insert([
                'user_id' => $user->id,
                'subscription_type' => 'HABBO_CLUB',
                'timestamp_start' => now()->unix(),
                'duration' => (int) (setting('hc_on_register_duration') ?: 0),
                'active' => 1,
            ]);
        }

        UserCurrency::insert([
            [
                'user_id' => $user->id,
                'type' => 0,
                'amount' => $user->username === 'Admin' ? 0 : (setting('start_duckets') ?: 0),
            ],
            [
                'user_id' => $user->id,
                'type' => 5,
                'amount' => $user->username === 'Admin' ? 0 : (setting('start_diamonds') ?: 0),
            ],
            [
                'user_id' => $user->id,
                'type' => 101,
                'amount' => $user->username === 'Admin' ? 0 : (setting('start_points') ?: 0),
            ],
        ]);

        CreateDefaultHome::for($user);
    }
}
