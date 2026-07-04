<?php

namespace App\Actions;

use App\Contracts\Rcon;
use App\Emulator\Contracts\CurrencyRepository;
use App\Enums\CurrencyTypes;
use App\Models\User;

class SendCurrency
{
    public function __construct(
        private readonly Rcon $rcon,
        private readonly CurrencyRepository $currencies,
    ) {}

    /**
     * Adjust a player's currency by a signed amount: live via Rcon when the
     * emulator is online, otherwise straight to the database.
     */
    public function execute(User $user, CurrencyTypes $currency, int $amount): void
    {
        if ($amount === 0) {
            return;
        }

        $this->rcon->isConnected()
            ? $this->rcon->giveCurrency($user, $currency, $amount)
            : $this->currencies->give($user, $currency, $amount);
    }
}
