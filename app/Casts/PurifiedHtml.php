<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Facades\Purify;
use UnexpectedValueException;

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
        if ($value === null) {
            return null;
        }

        $cleaned = Purify::clean((string) $value);

        if (! is_string($cleaned)) {
            throw new UnexpectedValueException('Purified HTML must be a string.');
        }

        return $cleaned;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}
