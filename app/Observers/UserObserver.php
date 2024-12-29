<?php

namespace App\Observers;

use App\Models\Game\Player\UserCurrency;
use App\Models\User;
use App\Services\RconService;

class UserObserver
{
    public function created(User $user): void
    {
        $user->settings()->create([
            'last_hc_payday' => setting('give_hc_on_register') == '1' ? now()->addYears(10)->unix() : 0,
        ]);

        if (setting('give_hc_on_register') == '1') {
            $user->hcSubscription()->insert([
                'user_id' => $user->id,
                'subscription_type' => 'HABBO_CLUB',
                'timestamp_start' => now()->unix(),
                'duration' => (int) setting('hc_on_register_duration'),
                'active' => 1,
            ]);
        }

        UserCurrency::insert([
            [
                'user_id' => $user->id,
                'type' => 0,
                'amount' => $user->username === 'Admin' ? 0 : setting('start_duckets'),
            ],
            [
                'user_id' => $user->id,
                'type' => 5,
                'amount' => $user->username === 'Admin' ? 0 : setting('start_diamonds'),
            ],
            [
                'user_id' => $user->id,
                'type' => 101,
                'amount' => $user->username === 'Admin' ? 0 : setting('start_points'),
            ],
        ]);
    }

    public function updated(User $user, RconService $rconService): void
    {
        if ($rconService->isConnected() && $user->online) {
            if ($user->isDirty('rank')) {
                $rconService->setRank($user, $user->rank);
            }
            
            $rconService->disconnectUser($user);
        }
    }
}
