<?php

namespace App\Rules;

use App\Models\Miscellaneous\WebsiteInstallation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateInstallationKeyRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $installationKey = WebsiteInstallation::query()->oldest('id')->value('installation_key');

        if (! is_string($value)
            || ! is_string($installationKey)
            || ! hash_equals($installationKey, $value)) {
            $fail('The :attribute does not match');
        }
    }
}
