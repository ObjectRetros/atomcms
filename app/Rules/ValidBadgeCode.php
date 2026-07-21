<?php

namespace App\Rules;

use App\Support\BadgeCode;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;

class ValidBadgeCode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            BadgeCode::normalize(is_string($value) ? $value : '');
        } catch (InvalidArgumentException $exception) {
            $fail($exception->getMessage());
        }
    }
}
