<?php

namespace App\Emulator\Drivers\Plus;

use App\Emulator\Contracts\CurrencyRepository;
use App\Enums\CurrencyTypes;
use App\Models\User;

/**
 * Plus EMU keeps every currency as a column on the users table:
 * credits, activity_points (duckets), vip_points (diamonds), gotw_points.
 */
class PlusCurrencyRepository implements CurrencyRepository
{
    public function balance(User $user, CurrencyTypes $currency): int
    {
        return (int) $user->getAttribute($this->column($currency));
    }

    public function give(User $user, CurrencyTypes $currency, int $amount): void
    {
        if ($amount === 0) {
            return;
        }

        $column = $this->column($currency);
        $query = User::whereKey($user->id);

        $amount > 0
            ? $query->increment($column, $amount)
            : $query->decrement($column, abs($amount));
    }

    public function deduct(User $user, CurrencyTypes $currency, int $amount): bool
    {
        $column = $this->column($currency);

        return User::whereKey($user->id)
            ->where($column, '>=', $amount)
            ->decrement($column, $amount) === 1;
    }

    private function column(CurrencyTypes $currency): string
    {
        return match ($currency) {
            CurrencyTypes::Credits => 'credits',
            CurrencyTypes::Duckets => 'activity_points',
            CurrencyTypes::Diamonds => 'vip_points',
            CurrencyTypes::Points => 'gotw_points',
        };
    }
}
