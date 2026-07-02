<?php

namespace App\Actions;

use App\Enums\CurrencyTypes;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeductCurrency
{
    /**
     * Atomically charge a user, returning false when they cannot afford it.
     */
    public function execute(User $user, string $currencyType, int $amount): bool
    {
        return DB::transaction(fn (): bool => $this->charge($user, $currencyType, $amount));
    }

    private function charge(User $user, string $currencyType, int $amount): bool
    {
        if ($currencyType === 'credits') {
            return User::whereKey($user->id)
                ->where('credits', '>=', $amount)
                ->decrement('credits', $amount) === 1;
        }

        $type = CurrencyTypes::fromCurrencyName($currencyType);

        return $type !== null && $user->currencies()
            ->where('type', $type->value)
            ->where('amount', '>=', $amount)
            ->decrement('amount', $amount) === 1;
    }
}
