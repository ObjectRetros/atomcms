<x-app-layout>
    @push('title', __('Two factor'))

    <div class="col-span-12 flex flex-col gap-y-3 md:col-span-3">
        <x-user.settings.settings-navigation />
    </div>

    <div class="col-span-12 flex flex-col gap-y-3 md:col-span-9">
        <x-content.content-card icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Two factor authentication') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Add an extra layer of security to your account by enabling two-factor authentication') }}
            </x-slot:under-title>

            <!-- 2FA enabled, we display the QR code : -->
            @if (auth()->user()->two_factor_confirmed)
                <form action="{{ route('user.two-factor.disable') }}" method="post">
                    @csrf
                    @method('delete')

                    <x-form.danger-button>
                        {{ __('Disable 2FA') }}
                    </x-form.danger-button>
                </form>

                {{-- 2FA enabled but not yet confirmed, we show the QRcode and ask for confirmation --}}
            @elseif(auth()->user()->two_factor_secret)
                <p>{{ __('Validate your two-factor enabling by scanning the following QR-code and enter your auto-generated 2-factor code from your phone.') }}
                </p>

                <div class="mt-4 flex flex-col items-center md:flex-row md:items-start md:justify-center">
                    <div class="flex gap-x-8 rounded bg-gray-100 px-4 py-2">
                        <span class="flex items-center">
                            {!! auth()->user()->twoFactorQrCodeSvg() !!}
                        </span>

                        <div>
                            <strong>
                                {{ __('Recovery codes:') }}
                            </strong>

                            <ul>
                                @foreach (auth()->user()->recoveryCodes() as $code)
                                    <li>{{ $code }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="mt-2 flex justify-center text-xs italic text-gray-600">
                    <div class="w-full lg:w-[480px]">
                        {{ __('Please save your recovery codes somewhere safe! If you lose access to your 2FA codes, those recovery codes will be needed to regain access your account.') }}
                    </div>
                </div>

                <form action="{{ route('two-factor.verify') }}" method="POST" class="mt-8">
                    @csrf

                    <x-form.label for="code">
                        {{ __('Code') }}

                        <x-slot:info>
                            {{ __('Please scan the QR-code above with your phone to retrieve your two-factor authentication code.') }}
                        </x-slot:info>
                    </x-form.label>

                    <x-form.input classes="mb-3" name="code" placeholder="{{ __('Code') }}" />

                    @if (setting('google_recaptcha_enabled'))
                        <div class="g-recaptcha" data-sitekey="{{ config('habbo.site.recaptcha_site_key') }}"></div>
                    @endif

                    @if (setting('cloudflare_turnstile_enabled'))
                        <x-turnstile />
                    @endif

                    <x-form.secondary-button classes="mt-4">
                        {{ __('Verify 2FA') }}
                    </x-form.secondary-button>
                </form>
            @else
                <div class="flex flex-col items-end">
                    <div class="flex w-full flex-col gap-y-3 dark:text-gray-100">
                        <p>
                            {{ __('Here at :hotel we take security very serious and therefore we offer you as a user a way to secure your beloved account even further, by allowing you to enable Googles 2-factor authentication!', ['hotel' => setting('hotel_name')]) }}
                        </p>

                        <p>
                            {{ __('2-factor authentication adds an extra layer of security to your account, making it physical impossible to access it without having access to your mobile phone as only your phone will contain the 2-factor authentication code which will be re-generated every 30 seconds automatically') }}
                        </p>
                    </div>

                    <form action="{{ route('user.two-factor.enable') }}" method="post" class="mt-8">
                        @csrf
                        <x-form.secondary-button>
                            {{ __('Activate 2FA') }}
                        </x-form.secondary-button>
                    </form>
                </div>
            @endif
        </x-content.content-card>
    </div>
</x-app-layout>
