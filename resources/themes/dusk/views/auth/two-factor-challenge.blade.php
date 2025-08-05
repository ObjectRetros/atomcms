<x-app-layout>
    @push('title', __('Two-Factor Authentication'))

    <div class="col-span-12 flex flex-col gap-y-3 md:col-span-9 md:col-start-4">
        <x-content.content-card icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Two-Factor Authentication') }}
            </x-slot:title>
            <x-slot:under-title>
                {{ __('Please enter your two-factor authentication code or one of your recovery codes.') }}
            </x-slot:under-title>

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.login') }}">
                @csrf

                <!-- Two-Factor Code -->
                <div>
                    <x-form.label for="code">
                        {{ __('Code') }}
                        <x-slot:info>
                            {{ __('Enter the two-factor authentication code from your authenticator app.') }}
                        </x-slot:info>
                    </x-form.label>
                    <x-form.input id="code" name="code" type="text" placeholder="{{ __('Code') }}" class="mb-3" />
                </div>

                <!-- Recovery Code -->
                <div class="mt-4">
                    <x-form.label for="recovery_code">
                        {{ __('Recovery Code') }}
                        <x-slot:info>
                            {{ __('Enter one of your recovery codes if you cannot access your authenticator app.') }}
                        </x-slot:info>
                    </x-form.label>
                    <x-form.input id="recovery_code" name="recovery_code" type="text" placeholder="{{ __('Recovery Code') }}" class="mb-3" />
                </div>

                @if (setting('google_recaptcha_enabled'))
                    <div class="g-recaptcha" data-sitekey="{{ config('habbo.site.recaptcha_site_key') }}"></div>
                @endif
                @if (setting('cloudflare_turnstile_enabled'))
                    <x-turnstile />
                @endif

                <x-form.secondary-button type="submit" class="mt-4">
                    {{ __('Verify') }}
                </x-form.secondary-button>
            </form>
        </x-content.content-card>
    </div>
</x-app-layout>