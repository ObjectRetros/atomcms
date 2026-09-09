<?php

namespace App\Data;

use App\Models\User;

final readonly class TwoFactorSetupData
{
    /** @param array<int, string> $recovery_codes */
    public function __construct(
        public bool $enabled,
        public ?string $qr_code,
        public array $recovery_codes,
    ) {}

    public static function from(User $user): self
    {
        $enabled = $user->hasEnabledTwoFactorAuthentication();

        return new self(
            $enabled,
            $user->two_factor_secret && ! $enabled ? $user->twoFactorQrCodeSvg() : null,
            $user->two_factor_recovery_codes ? $user->recoveryCodes() : [],
        );
    }
}
