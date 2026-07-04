<?php

namespace App\Emulator\Drivers\Arcturus;

use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Data\Currency;
use App\Models\User;
use LogicException;

/**
 * Arcturus keeps credits on the users table and every other currency as a
 * typed row in users_currency.
 */
class ArcturusCurrencyRepository implements CurrencyRepository
{
    public function balance(User $user, Currency $currency): int
    {
        if ($currency === Currency::Credits) {
            return (int) $user->credits;
        }

        return (int) ($user->currencies()->where('type', $this->type($currency))->value('amount') ?? 0);
    }

    public function give(User $user, Currency $currency, int $amount): void
    {
        if ($currency === Currency::Credits) {
            $user->increment('credits', $amount);

            return;
        }

        $user->currencies()
            ->firstOrCreate(['type' => $this->type($currency)], ['amount' => 0])
            ->increment('amount', $amount);
    }

    public function deduct(User $user, Currency $currency, int $amount): bool
    {
        if ($currency === Currency::Credits) {
            return User::whereKey($user->id)
                ->where('credits', '>=', $amount)
                ->decrement('credits', $amount) === 1;
        }

        return $user->currencies()
            ->where('type', $this->type($currency))
            ->where('amount', '>=', $amount)
            ->decrement('amount', $amount) === 1;
    }

    private function type(Currency $currency): int
    {
        return match ($currency) {
            Currency::Duckets => 0,
            Currency::Diamonds => 5,
            Currency::Points => 101,
            Currency::Credits => throw new LogicException('Credits live on the users table, not users_currency.'),
        };
    }
}
