<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;

class TwoFactorAuthentication extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected string $view = 'filament.pages.two-factor-authentication';

    public string $currentPassword = '';

    public string $code = '';

    public function enable(): void
    {
        $this->throttle();
        $this->validate(['currentPassword' => ['required', 'current_password:web']]);

        app(EnableTwoFactorAuthentication::class)($this->currentUser());
        $this->reset('currentPassword', 'code');
    }

    public function confirm(): void
    {
        $this->throttle();
        $this->validate(['code' => ['required', 'string', 'size:6']]);

        app(ConfirmTwoFactorAuthentication::class)($this->currentUser(), $this->code);
        $this->reset('code');

        Notification::make()->success()->title(__('Two-factor authentication has been confirmed.'))->send();
    }

    public function disable(): void
    {
        $this->throttle();
        $this->validate(['currentPassword' => ['required', 'current_password:web']]);

        app(DisableTwoFactorAuthentication::class)($this->currentUser());
        $this->reset('currentPassword', 'code');
    }

    protected function currentUser(): User
    {
        $user = Filament::auth()->user();
        abort_unless($user instanceof User, 403);

        return $user->refresh();
    }

    protected function throttle(): void
    {
        $key = 'housekeeping-two-factor-settings:' . $this->currentUser()->getAuthIdentifier();

        if (RateLimiter::tooManyAttempts($key, 6)) {
            throw ValidationException::withMessages([
                'code' => __('Too many attempts. Please try again later.'),
            ]);
        }

        RateLimiter::hit($key);
    }

    protected function getViewData(): array
    {
        return ['user' => $this->currentUser()];
    }
}
