<?php

namespace App\Emulator\Contracts;

use App\Emulator\Data\Currency;
use App\Models\User;

/**
 * Reads and writes a player's currency balances on the emulator database.
 *
 * This is the offline (direct database) path. Writes made while the emulator
 * is online should still go through Rcon so the running server stays in sync;
 * the currency actions choose between the two.
 */
interface CurrencyRepository
{
    public function balance(User $user, Currency $currency): int;

    public function give(User $user, Currency $currency, int $amount): void;

    /**
     * Atomically remove the amount, returning false if the balance is short.
     */
    public function deduct(User $user, Currency $currency, int $amount): bool;
}
