<?php

namespace App\Emulator\Drivers\Arcturus;

use App\Data\PublicUserData;
use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Data\LeaderboardEntry;
use App\Enums\CurrencyTypes;
use App\Models\Game\Player\UserCurrency;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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

        return (int) ($this->currencies($user)->where('type', $currency->value)->value('amount') ?? 0);
    }

    public function give(User $user, CurrencyTypes $currency, int $amount): void
    {
        if ($amount === 0) {
            return;
        }

        if ($currency === CurrencyTypes::Credits) {
            $this->adjust(User::whereKey($user->id), 'credits', $amount);

            return;
        }

        // users_currency has a composite key (user_id, type) that Eloquent
        // cannot address through a model, so adjust via the relation query.
        UserCurrency::query()->firstOrCreate(
            ['user_id' => $user->id, 'type' => $currency->value],
            ['amount' => 0],
        );

        $this->adjust($this->currencies($user)->where('type', $currency->value), 'amount', $amount);
    }

    public function deduct(User $user, CurrencyTypes $currency, int $amount): bool
    {
        // A zero decrement changes no rows, which the driver would otherwise
        // report as an unaffordable purchase.
        if ($amount <= 0) {
            return true;
        }

        if ($currency === CurrencyTypes::Credits) {
            return User::whereKey($user->id)
                ->where('credits', '>=', $amount)
                ->decrement('credits', $amount) === 1;
        }

        return $this->currencies($user)
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
                ->get([...PublicUserData::COLUMNS, 'credits'])
                ->map(fn (User $user) => new LeaderboardEntry($user, (int) $user->credits));
        }

        return UserCurrency::query()
            ->where('type', $currency->value)
            ->whereNotIn('user_id', $excludeUserIds)
            ->orderByDesc('amount')
            ->limit($limit)
            ->with('user:' . implode(',', PublicUserData::COLUMNS))
            ->get()
            ->map(fn (UserCurrency $row) => $row->user === null ? null : new LeaderboardEntry($row->user, (int) $row->amount))
            ->filter()
            ->values();
    }

    /**
     * @param  Builder<covariant Model>  $query
     */
    private function adjust(Builder $query, string $column, int $amount): void
    {
        $amount > 0
            ? $query->increment($column, $amount)
            : $query->decrement($column, abs($amount));
    }

    /** @return Builder<UserCurrency> */
    private function currencies(User $user): Builder
    {
        return UserCurrency::query()->where('user_id', $user->id);
    }
}
