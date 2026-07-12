<x-app-layout>
    @push('title', __('Account settings'))

    <div class="col-span-12 flex flex-col gap-y-3 md:col-span-3">
        <x-user.settings.settings-navigation />
    </div>

    <div class="col-span-12 flex flex-col gap-y-3 md:col-span-9">
        <x-content.content-card icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Account settings') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Manage your account settings') }}
            </x-slot:under-title>

            <form action="{{ route('settings.account.update') }}" method="POST" class="flex flex-col gap-y-4">
                @method('PUT')
                @csrf

                <div class="flex flex-col gap-y-1">
                    <x-form.label for="mail">
                        {{ __('E-mail') }}

                        <x-slot:info>
                            {{ __('Make sure to use an email that you remember, if you ever lose your password, your email will be required.') }}
                        </x-slot:info>
                    </x-form.label>

                    <x-form.input name="mail" type="email" value="{{ $user->mail }}" :autofocus="true" />
                </div>

                @if ($user->settings?->allow_name_change)
                    <div class="flex flex-col gap-y-1">
                        <x-form.label for="username">
                            {{ __('Username') }}

                            <x-slot:info>
                                {{ __('Your username is what you and others will see in-game') }}
                            </x-slot:info>
                        </x-form.label>

                        <x-form.input name="username" value="{{ $user->username }}" />
                    </div>
                @endif

                <div class="flex flex-col gap-y-1">
                    <x-form.label for="motto">
                        {{ __('Motto') }}

                        <x-slot:info>
                            {{ __('Spice up your profile with a nice motto') }}
                        </x-slot:info>
                    </x-form.label>

                    <x-form.input name="motto" value="{{ $user->motto }}" />
                </div>

                <div class="flex flex-col gap-y-1">
                    <x-form.label for="current_password">
                        {{ __('Current password') }}

                        <x-slot:info>
                            {{ __('Required to change your e-mail address.') }}
                        </x-slot:info>
                    </x-form.label>

                    <x-form.input name="current_password" type="password" autocomplete="current-password" />
                </div>

                @if (setting('google_recaptcha_enabled'))
                    <div class="g-recaptcha" data-sitekey="{{ config('habbo.site.recaptcha_site_key') }}"></div>
                @endif

                @if (setting('cloudflare_turnstile_enabled'))
                    <x-turnstile />
                @endif

                <div class="flex w-full justify-start md:justify-end">
                    <x-form.secondary-button classes="lg:w-1/4">
                        {{ __('Update settings') }}
                    </x-form.secondary-button>
                </div>
            </form>
        </x-content.content-card>
    </div>
</x-app-layout>
