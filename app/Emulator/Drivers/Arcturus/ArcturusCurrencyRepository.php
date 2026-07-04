<?php

namespace App\Emulator\Drivers\Arcturus;

use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Data\LeaderboardEntry;
use App\Enums\CurrencyTypes;
use App\Models\Game\Player\UserCurrency;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    public function topBy(CurrencyTypes $currency, int $limit, array $excludeUserIds = []): Collection
    {
        if ($currency === CurrencyTypes::Credits) {
            return User::query()
                ->whereNotIn('id', $excludeUserIds)
                ->orderByDesc('credits')
                ->limit($limit)
                ->get(['id', 'username', 'look', 'credits'])
                ->map(fn (User $user) => new LeaderboardEntry($user, (int) $user->credits));
        }

        return UserCurrency::query()
            ->where('type', $currency->value)
            ->whereNotIn('user_id', $excludeUserIds)
            ->orderByDesc('amount')
            ->limit($limit)
            ->with('user:id,username,look')
            ->get()
            ->map(fn (UserCurrency $row) => $row->user === null ? null : new LeaderboardEntry($row->user, (int) $row->amount))
            ->filter()
            ->values();
    }

    private function adjust(Model $model, string $column, int $amount): void
    {
        $amount > 0
            ? $model->increment($column, $amount)
            : $model->decrement($column, abs($amount));
    }
}
