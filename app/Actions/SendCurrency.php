<?php

namespace App\Actions;

use App\Enums\CurrencyTypes;
use App\Models\User;
use App\Services\RconService;

class SendCurrency
{
    public function __construct(protected RconService $rcon) {}

    public function execute(User $user, string $type, ?int $amount): bool
    {
        if ($amount === null || $amount === 0) {
            return false;
        }

        if ($this->rcon->isConnected) {
            if (! in_array($type, ['credits', 'duckets', 'diamonds', 'points'], true)) {
                return false;
            }

            match ($type) {
                'credits' => $this->rcon->giveCredits($user, $amount),
                'duckets' => $this->rcon->giveDuckets($user, $amount),
                'diamonds' => $this->rcon->giveDiamonds($user, $amount),
                'points' => $this->rcon->giveGotw($user, $amount),
            };

            return true;
        }

        return match ($type) {
            'credits' => $this->adjustColumn($user, 'credits', $amount),
            'duckets' => $this->adjustCurrency($user, CurrencyTypes::Duckets, $amount),
            'diamonds' => $this->adjustCurrency($user, CurrencyTypes::Diamonds, $amount),
            'points' => $this->adjustCurrency($user, CurrencyTypes::Points, $amount),
            default => false,
        };
    }

    private function adjustColumn(User $user, string $column, int $amount): bool
    {
        $amount > 0
            ? $user->increment($column, $amount)
            : $user->decrement($column, abs($amount));

        return true;
    }

    private function adjustCurrency(User $user, CurrencyTypes $type, int $amount): bool
    {
        $query = $user->currencies()->where('type', $type->value);

        $amount > 0
            ? $query->increment('amount', $amount)
            : $query->decrement('amount', abs($amount));

        return true;
    }
}
