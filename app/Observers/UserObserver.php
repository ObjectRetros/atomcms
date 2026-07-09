<?php

namespace App\Observers;

use App\Actions\Home\CreateDefaultHome;
use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Data\Feature;
use App\Emulator\Emulator;
use App\Enums\CurrencyTypes;
use App\Models\User;

class UserObserver
{
    public function __construct(private readonly CurrencyRepository $currencies) {}

    public function created(User $user): void
    {
        if (Emulator::supports(Feature::EmulatorUserSettings)) {
            $this->createEmulatorSettings($user);
        }

        $this->grantStartingBalances($user);

        CreateDefaultHome::for($user);
    }

    /**
     * Arcturus-family emulators keep per-player settings and club
     * subscriptions in their own tables; create those rows at registration.
     */
    private function createEmulatorSettings(User $user): void
    {
        $giveHc = (setting('give_hc_on_register') ?: '0') == '1';

        $user->settings()->create([
            'last_hc_payday' => $giveHc ? now()->addYears(10)->unix() : 0,
        ]);

        if ($giveHc) {
            $user->hcSubscription()->insert([
                'user_id' => $user->id,
                'subscription_type' => 'HABBO_CLUB',
                'timestamp_start' => now()->unix(),
                'duration' => (int) (setting('hc_on_register_duration') ?: 0),
                'active' => 1,
            ]);
        }
    }

    /**
     * Starting balances go through the currency driver, so registration works
     * on every emulator schema. Credits are a column on users and are set by
     * the registration action itself.
     */
    private function grantStartingBalances(User $user): void
    {
        $startingBalances = [
            [CurrencyTypes::Duckets, 'start_duckets'],
            [CurrencyTypes::Diamonds, 'start_diamonds'],
            [CurrencyTypes::Points, 'start_points'],
        ];

        foreach ($startingBalances as [$currency, $settingKey]) {
            $amount = $user->username === 'Admin' ? 0 : (int) (setting($settingKey) ?: 0);

            if ($amount > 0) {
                $this->currencies->give($user, $currency, $amount);
            }
        }
    }
}
