<?php

namespace App\Emulator\Contracts;

use App\Enums\CurrencyTypes;
use App\Models\User;

/**
 * Reads and writes a player's currency balances on the emulator database.
 *
 * The CMS speaks in CurrencyTypes; each driver maps those onto its emulator's
 * own storage (a typed row, a dedicated column, ...). This is the offline
 * (direct database) path - writes made while the emulator is online go through
 * Rcon instead, and the currency action chooses between the two.
 */
interface CurrencyRepository
{
    public function balance(User $user, CurrencyTypes $currency): int;

    /**
     * Adjust the balance by a signed amount (positive grants, negative removes).
     */
    public function give(User $user, CurrencyTypes $currency, int $amount): void;

    /**
     * Atomically remove the amount, returning false if the balance is short.
     */
    public function deduct(User $user, CurrencyTypes $currency, int $amount): bool;
}
