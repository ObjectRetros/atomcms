<x-app-layout>
    @push('title', __('Two factor'))

    <div class="col-span-12 flex flex-col gap-y-3 md:col-span-3">
        <x-user.settings.settings-navigation />
    </div>

    <div class="col-span-12 flex flex-col gap-y-3 md:col-span-9">
        <x-content.content-card icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Confirm your password') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('You must confirm your password to continue') }}
            </x-slot:under-title>

            <form method="POST" action="/user/confirm-password">
                @csrf

                <!-- Password -->
                <div class="flex flex-col gap-y-2 mb-3">
                    <div>
                        <x-form.label for="password">
                            {{ __('Password') }}

                            <x-slot:info>
                                {{ __('You must confirm your current password before being able to toggle 2FA.') }}
                            </x-slot:info>
                        </x-form.label>

                        <x-form.input name="password" type="password"
                            placeholder="{{ __('Enter your current password') }}" />
                    </div>
                </div>

                @if (setting('google_recaptcha_enabled'))
                    <div class="mt-4 g-recaptcha" data-sitekey="{{ config('habbo.site.recaptcha_site_key') }}"></div>
                @endif

                @if (setting('cloudflare_turnstile_enabled'))
                    <x-turnstile />
                @endif

                <div class="mt-4">
                    <x-form.primary-button>
                        {{ __('Confirm password') }}
                    </x-form.primary-button>
                </div>
            </form>
        </x-content.content-card>
    </div>
</x-app-layout>
