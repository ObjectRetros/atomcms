<x-app-layout>
    @push('title', __('Two factor'))

    <div class="col-span-12 flex flex-col gap-y-0">
        <x-user.settings.settings-navigation />

        <x-content.content-section icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Two factor authentication') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Add an extra layer of security to your account by enabling two-factor authentication') }}
            </x-slot:under-title>

            <div class="max-w-lg">
                @if(auth()->user()->two_factor_confirmed)
                    <form action="/user/two-factor-authentication" method="post">
                        @csrf
                        @method('delete')
                        <x-form.danger-button>
                            {{ __('Disable 2FA') }}
                        </x-form.danger-button>
                    </form>

                @elseif(auth()->user()->two_factor_secret)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('Validate your two-factor enabling by scanning the following QR-code and enter your auto-generated 2-factor code from your phone.') }}
                    </p>

                    <div class="flex flex-col items-center md:flex-row md:items-start md:justify-center mt-4">
                        <div class="rounded bg-gray-100 dark:bg-gray-800 py-2 px-4 flex gap-x-8">
                            <span class="flex items-center">
                                {!! auth()->user()->twoFactorQrCodeSvg() !!}
                            </span>
                            <div>
                                <strong class="text-sm text-gray-700 dark:text-gray-200">
                                    {{ __('Recovery codes:') }}
                                </strong>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 mt-1 space-y-1">
                                    @foreach(auth()->user()->recoveryCodes() as $code)
                                        <li>{{ $code }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 italic mt-2 text-center">
                        {{ __('Please save your recovery codes somewhere safe! If you lose access to your 2FA codes, those recovery codes will be needed to regain access your account.') }}
                    </p>

                    <form action="{{ route('two-factor.verify') }}" method="POST" class="mt-6 flex flex-col gap-y-3">
                        @csrf
                        <x-form.label for="code">
                            {{ __('Code') }}
                            <x-slot:info>
                                {{ __('Please scan the QR-code above with your phone to retrieve your two-factor authentication code.') }}
                            </x-slot:info>
                        </x-form.label>
                        <x-form.input name="code" placeholder="{{ __('Code') }}" />
                        <x-form.secondary-button>
                            {{ __('Verify 2FA') }}
                        </x-form.secondary-button>
                    </form>

                @else
                    <div class="flex flex-col gap-y-3 dark:text-gray-300 text-sm mb-6">
                        <p>{{ __('Here at :hotel we take security very serious and therefore we offer you as a user a way to secure your beloved account even further, by allowing you to enable Googles 2-factor authentication!', ['hotel' => setting('hotel_name')]) }}</p>
                        <p>{{ __('2-factor authentication adds an extra layer of security to your account, making it physical impossible to access it without having access to your mobile phone as only your phone will contain the 2-factor authentication code which will be re-generated every 30 seconds automatically') }}</p>
                    </div>
                    <form action="/user/two-factor-authentication" method="post">
                        @csrf
                        <x-form.secondary-button>
                            {{ __('Activate 2FA') }}
                        </x-form.secondary-button>
                    </form>
                @endif
            </div>
        </x-content.content-section>
    </div>
</x-app-layout>
