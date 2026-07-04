<?php

namespace App\Emulator\Drivers\Arcturus;

use App\Emulator\Contracts\CurrencyRepository;
use App\Enums\CurrencyTypes;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Arcturus keeps credits on the users table and every other currency as a typed
 * row in users_currency (users_currency.type matches the CurrencyTypes value).
 */
class ArcturusCurrencyRepository implements CurrencyRepository
{
    public function balance(User $user, CurrencyTypes $currency): int
    {
        if ($currency === CurrencyTypes::Credits) {
            return (int) $user->credits;
        }

        return (int) ($user->currencies()->where('type', $currency->value)->value('amount') ?? 0);
    }

    public function give(User $user, CurrencyTypes $currency, int $amount): void
    {
        if ($amount === 0) {
            return;
        }

        if ($currency === CurrencyTypes::Credits) {
            $this->adjust($user, 'credits', $amount);

            return;
        }

        $row = $user->currencies()->firstOrCreate(['type' => $currency->value], ['amount' => 0]);
        $this->adjust($row, 'amount', $amount);
    }

    public function deduct(User $user, CurrencyTypes $currency, int $amount): bool
    {
        if ($currency === CurrencyTypes::Credits) {
            return User::whereKey($user->id)
                ->where('credits', '>=', $amount)
                ->decrement('credits', $amount) === 1;
        }

        return $user->currencies()
            ->where('type', $currency->value)
            ->where('amount', '>=', $amount)
            ->decrement('amount', $amount) === 1;
    }

    private function adjust(Model $model, string $column, int $amount): void
    {
        $amount > 0
            ? $model->increment($column, $amount)
            : $model->decrement($column, abs($amount));
    }
}
