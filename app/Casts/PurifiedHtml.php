<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Facades\Purify;

/**
 * Sanitises rich-text HTML on read, so stored editor content (including legacy
 * rows) is safe wherever it is rendered with {!! !!}.
 *
 * @implements CastsAttributes<string|null, string|null>
 */
class PurifiedHtml implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value === null ? null : Purify::clean($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}
