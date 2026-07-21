<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Facades\Purify;

/**
 * Sanitises rich-text HTML before persistence so every read surface receives
 * the same safe value without repeatedly running HTML Purifier.
 *
 * @implements CastsAttributes<string|null, string|null>
 */
class PurifiedHtml implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value === null ? null : (string) $value;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value === null ? null : Purify::clean((string) $value);
    }
}
