<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CurrentPasswordRule implements InvokableRule
{
    /**
     * Run the validation rule.
     */
    public function __invoke(string $attribute, mixed $value, Closure $fail): void
    {
        $user = Auth::user();

        if (! $user instanceof User || ! is_string($value) || ! Hash::check($value, $user->password)) {
            $fail('It seems like your current password is wrong.');
        }
    }
}
