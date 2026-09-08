<?php

namespace App\Filament\Auth;

use App\Actions\Fortify\VerifyTwoFactorCode;
use App\Filament\Pages\TwoFactorAuthentication;
use App\Models\User;
use Closure;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Auth\Authenticatable;
use SensitiveParameter;

class FortifyTwoFactorAuthentication implements MultiFactorAuthenticationProvider
{
    public function getId(): string
    {
        return 'fortify';
    }

    public function getLoginFormLabel(): string
    {
        return __('Authenticator app');
    }

    public function isEnabled(Authenticatable $user): bool
    {
        return $user instanceof User && $user->hasEnabledTwoFactorAuthentication();
    }

    public function getManagementSchemaComponents(): array
    {
        return [Action::make('manageTwoFactorAuthentication')
            ->label(__('Manage two-factor authentication'))
            ->url(TwoFactorAuthentication::getUrl())];
    }

    public function getChallengeFormComponents(Authenticatable $user): array
    {
        return [TextInput::make('code')
            ->label(__('Authentication or recovery code'))
            ->autocomplete('one-time-code')
            ->required()
            ->maxLength(255)
            ->rule(fn (): Closure => function (string $attribute, #[SensitiveParameter] mixed $value, Closure $fail) use ($user): void {
                if ($user instanceof User && is_string($value) && app(VerifyTwoFactorCode::class)($user, $value, $value)) {
                    return;
                }

                $fail(__('Invalid Two Factor Authentication code'));
            })];
    }
}
