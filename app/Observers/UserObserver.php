<?php

namespace App\Observers;

use App\Actions\Home\CreateDefaultHome;
use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Contracts\PlayerRepository;
use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Enums\CurrencyTypes;
use App\Models\User;

class UserObserver
{
    public function __construct(
        private readonly CurrencyRepository $currencies,
        private readonly PlayerRepository $players,
        private readonly PlayerSettingsRepository $settings,
        private readonly CreateDefaultHome $defaultHome,
    ) {}

    public function created(User $user): void
    {
        $this->players->created($user);

        $this->settings->created($user);

        $this->grantStartingBalances($user);

        $this->defaultHome->execute($user);
    }

    public function updated(User $user): void
    {
        $this->players->updated($user);
    }

    public function deleted(User $user): void
    {
        $this->players->deleted($user);
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
