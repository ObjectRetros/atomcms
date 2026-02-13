<?php

namespace App\Models\Traits;

use App\Enums\CurrencyTypes;
use App\Models\Game\Player\UserCurrency;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasCurrency
{
    public function currencies(): HasMany
    {
        return $this->hasMany(UserCurrency::class, 'user_id');
    }

    public function currency(string $currency): int
    {
        if (! $this->relationLoaded('currencies')) {
            $this->load('currencies');
        }

        $type = match ($currency) {
            'duckets' => CurrencyTypes::Duckets->value,
            'diamonds' => CurrencyTypes::Diamonds->value,
            'points' => CurrencyTypes::Points->value,
            default => null,
        };

        if ($type === null) {
            return 0;
        }

        return $this->currencies->where('type', $type)->first()?->amount ?? 0;
    }
}
