<x-filament-panels::page>
    <x-filament::section>
        @if ($user->hasEnabledTwoFactorAuthentication())
            <p>{{ __('Two-factor authentication is enabled. Keep these recovery codes somewhere safe. Each code can be used once.') }}</p>
            <ul class="mt-4 space-y-1 font-mono">
                @foreach ($user->recoveryCodes() as $recoveryCode)
                    <li>{{ $recoveryCode }}</li>
                @endforeach
            </ul>
            <x-filament::button tag="a" :href="\App\Filament\Pages\Dashboard::getUrl()" class="mt-4">
                {{ __('Continue to housekeeping') }}
            </x-filament::button>
        @elseif ($user->two_factor_secret)
            <p>{{ __('Scan this QR code with your authenticator app, then enter the six-digit code to finish setup.') }}</p>
            <div class="my-4 w-fit bg-white p-4">{!! $user->twoFactorQrCodeSvg() !!}</div>
            <form wire:submit="confirm" class="space-y-4">
                <label for="two-factor-code">{{ __('Authentication code') }}</label>
                <x-filament::input.wrapper>
                    <x-filament::input id="two-factor-code" wire:model="code" autocomplete="one-time-code" inputmode="numeric" maxlength="6" required />
                </x-filament::input.wrapper>
                <x-filament::button type="submit">{{ __('Confirm two-factor authentication') }}</x-filament::button>
            </form>
        @else
            <p>{{ __('Protect your account with an authenticator app. Your existing account and housekeeping use the same two-factor authentication.') }}</p>
        @endif

        <form wire:submit="{{ $user->two_factor_secret ? 'disable' : 'enable' }}" class="mt-6 space-y-4">
            <label for="two-factor-password">{{ __('Current password') }}</label>
            <x-filament::input.wrapper>
                <x-filament::input id="two-factor-password" type="password" wire:model="currentPassword" autocomplete="current-password" required />
            </x-filament::input.wrapper>
            @error('currentPassword')
                <p role="alert">{{ $message }}</p>
            @enderror
            <x-filament::button type="submit" :color="$user->two_factor_secret ? 'danger' : 'primary'">
                {{ $user->two_factor_secret ? __('Disable two-factor authentication') : __('Enable two-factor authentication') }}
            </x-filament::button>
        </form>
        @error('code')
            <p role="alert" class="mt-4">{{ $message }}</p>
        @enderror
    </x-filament::section>
</x-filament-panels::page>
