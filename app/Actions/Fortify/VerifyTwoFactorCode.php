<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;
use SensitiveParameter;

class VerifyTwoFactorCode
{
    public function __construct(private TwoFactorAuthenticationProvider $provider) {}

    public function __invoke(User $user, #[SensitiveParameter] ?string $code, #[SensitiveParameter] ?string $recoveryCode = null): bool
    {
        $secret = $user->two_factor_secret;

        if (! $user->hasEnabledTwoFactorAuthentication() || $secret === null) {
            return false;
        }

        if (filled($recoveryCode) && $user->getConnection()->transaction(function () use ($user, $recoveryCode): bool {
            $user = $user->newQuery()->whereKey($user->getKey())->lockForUpdate()->first();

            if (! $user?->hasEnabledTwoFactorAuthentication() || blank($user->two_factor_recovery_codes)) {
                return false;
            }

            foreach ($user->recoveryCodes() as $storedCode) {
                if (hash_equals($storedCode, $recoveryCode)) {
                    $user->replaceRecoveryCode($storedCode);

                    return true;
                }
            }

            return false;
        })) {
            return true;
        }

        return filled($code) && $this->provider->verify(
            Fortify::currentEncrypter()->decrypt($secret),
            $code,
        );
    }
}
