<?php

namespace App\Actions;

use App\Enums\CurrencyTypes;
use App\Services\RconService;

class SendCurrency
{
    public function __construct(protected RconService $rcon) {}

    public function execute($user, string $type, ?int $amount)
    {
        if (! $amount && $amount <= 0) {
            return false;
        }

        if ($this->rcon->isConnected) {
            match ($type) {
                'credits' => $this->rcon->giveCredits($user, $amount),
                'duckets' => $this->rcon->giveDuckets($user, $amount),
                'diamonds' => $this->rcon->giveDiamonds($user, $amount),
                'points' => $this->rcon->giveGotw($user, $amount),
                default => false,
            };
        } else {
            match ($type) {
                'credits' => $user->increment('credits', $amount),
                'duckets' => $user->currencies()->where('type', CurrencyTypes::Duckets)->increment('amount', $amount),
                'diamonds' => $user->currencies()->where('type', CurrencyTypes::Diamonds)->increment('amount', $amount),
                'points' => $user->currencies()->where('type', CurrencyTypes::Points)->increment('amount', $amount),
                default => false,
            };
        }
    }
}
