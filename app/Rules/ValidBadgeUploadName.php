<?php

namespace App\Rules;

use App\Support\BadgeCode;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ValidBadgeUploadName implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof TemporaryUploadedFile) {
            $fail('The badge upload is invalid.');

            return;
        }

        $name = $value->getClientOriginalName();

        if (basename($name) !== $name || strtolower(pathinfo($name, PATHINFO_EXTENSION)) !== 'gif') {
            $fail('The badge filename must be a GIF filename without a path.');

            return;
        }

        try {
            BadgeCode::normalize(strtoupper(pathinfo($name, PATHINFO_FILENAME)));
        } catch (InvalidArgumentException $exception) {
            $fail($exception->getMessage());
        }
    }
}
