<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class Password implements Rule
{
    /**
     * The minimum length of the password.
     */
    protected int $length = 6;

    /**
     * Indicates if the password must contain one uppercase character.
     */
    protected bool $requireUppercase = false;

    /**
     * Indicates if the password must contain one numeric digit.
     */
    protected bool $requireNumeric = false;

    /**
     * Indicates if the password must contain one special character.
     */
    protected bool $requireSpecialCharacter = false;

    /**
     * The message that should be used when validation fails.
     */
    protected ?string $message = null;

    /**
     * Determine if the validation rule passes.
     *
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        $value = is_scalar($value) ? (string) $value : '';

        if ($this->requireUppercase && Str::lower($value) === $value) {
            return false;
        }

        if ($this->requireNumeric && ! preg_match('/[0-9]/', $value)) {
            return false;
        }

        if ($this->requireSpecialCharacter && ! preg_match('/[\W_]/', $value)) {
            return false;
        }

        return Str::length($value) >= $this->length;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        if ($this->message) {
            return $this->message;
        }

        return match (true) {
            $this->requireUppercase
            && ! $this->requireNumeric
            && ! $this->requireSpecialCharacter => __(
                'The :attribute must be at least :length characters and contain at least one uppercase character.',
                [
                    'length' => $this->length,
                ],
            ),
            $this->requireNumeric
            && ! $this->requireUppercase
            && ! $this->requireSpecialCharacter => __(
                'The :attribute must be at least :length characters and contain at least one number.',
                [
                    'length' => $this->length,
                ],
            ),
            $this->requireSpecialCharacter
            && ! $this->requireUppercase
            && ! $this->requireNumeric => __(
                'The :attribute must be at least :length characters and contain at least one special character.',
                [
                    'length' => $this->length,
                ],
            ),
            $this->requireUppercase
            && $this->requireNumeric
            && ! $this->requireSpecialCharacter => __(
                'The :attribute must be at least :length characters and contain at least one uppercase character and one number.',
                [
                    'length' => $this->length,
                ],
            ),
            $this->requireUppercase
            && $this->requireSpecialCharacter
            && ! $this->requireNumeric => __(
                'The :attribute must be at least :length characters and contain at least one uppercase character and one special character.',
                [
                    'length' => $this->length,
                ],
            ),
            $this->requireUppercase
            && $this->requireNumeric
            && $this->requireSpecialCharacter => __(
                'The :attribute must be at least :length characters and contain at least one uppercase character, one number, and one special character.',
                [
                    'length' => $this->length,
                ],
            ),
            $this->requireNumeric
            && $this->requireSpecialCharacter
            && ! $this->requireUppercase => __(
                'The :attribute must be at least :length characters and contain at least one special character and one number.',
                [
                    'length' => $this->length,
                ],
            ),
            default => __('The :attribute must be at least :length characters.', [
                'length' => $this->length,
            ]),
        };
    }

    /**
     * Set the minimum length of the password.
     */
    public function length(int $length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Indicate that at least one uppercase character is required.
     */
    public function requireUppercase(): static
    {
        $this->requireUppercase = true;

        return $this;
    }

    /**
     * Indicate that at least one numeric digit is required.
     */
    public function requireNumeric(): static
    {
        $this->requireNumeric = true;

        return $this;
    }

    /**
     * Indicate that at least one special character is required.
     */
    public function requireSpecialCharacter(): static
    {
        $this->requireSpecialCharacter = true;

        return $this;
    }

    /**
     * Set the message that should be used when the rule fails.
     */
    public function withMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
