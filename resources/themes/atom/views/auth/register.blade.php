<x-app-layout>
    @push('title', __('Create account'))

    <!-- Validation Errors -->
    <x-messages.flash-messages />

    <div class="col-span-12 ">
        <div class="lg:px-[150px]">
            <x-content.content-section icon="hotel-icon" classes="flex flex-col gap-y-8">
                <x-slot:title>
                    {{ __('Create your account!') }}
                </x-slot:title>

                <x-slot:under-title>
                    {{ __('Create a free account, and be a part of a fun online world!') }}
                </x-slot:under-title>

                <div class="w-full flex justify-between pr-0 lg:pr-14">
                    <div class="w-full lg:w-[420px]">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Name -->
                            <div>
                                <div class="flex flex-col gap-y-2">
                                    <x-form.label for="username">
                                        {{ __('Username') }}

                                        <x-slot:info>
                                            {{ __('Your username is what you will have to use, when logging into :hotel. It is also what other users will know you as, so make sure you select a username that you like!', ['hotel' => setting('hotel_name')]) }}
                                        </x-slot:info>
                                    </x-form.label>
                                </div>

                                <x-form.input error-bag="register" name="username" type="text" value="{{ old('username') }}" placeholder="{{ __('Username') }}" :autofocus="true"/>
                            </div>

                            <!-- Email Address -->
                            <div class="mt-4">
                                <div class="flex flex-col gap-y-2">
                                    <x-form.label for="mail">
                                        {{ __('Email') }}

                                        <x-slot:info>
                                            {{ __('You will need your email if you were to ever forget your password, so make sure it is something that you remember.') }}
                                        </x-slot:info>
                                    </x-form.label>
                                </div>

                                <x-form.input error-bag="register" name="mail" type="email" value="{{ old('mail') }}" placeholder="{{ __('Enter your email') }}" />
                            </div>

                            <!-- Password -->
                            <div class="mt-4 bg-[#efefef] rounded-md p-3 flex flex-col gap-y-6 dark:bg-gray-900">
                                <div>
                                    <x-form.label for="password">
                                        {{ __('Password') }}

                                        <x-slot:info>
                                            {{ __('Your password must contain atleast 8 characters. Make sure to use a unique & secure password.') }}
                                        </x-slot:info>
                                    </x-form.label>

                                    <x-form.input error-bag="register" name="password" type="password" placeholder="{{ __('Choose a secure password') }}" />
                                </div>
                                <hr class="dark:border-gray-700">

                                <!-- Confirm Password -->
                                <div>
                                    <x-form.label for="password_confirmation">
                                        {{ __('Repeat Password') }}
                                    </x-form.label>

                                    <x-form.input error-bag="register" name="password_confirmation" type="password" placeholder="{{ __('Repeat your chosen password') }}" />
                                </div>
                            </div>

                            @if(setting('requires_beta_code'))
                                <div class="mt-4">
                                    <div class="flex flex-col gap-y-2">
                                        <x-form.label for="mail">
                                            {{ __('Beta code') }}

                                            <x-slot:info>
                                                {{ __('Enter the beta code you have been provided with') }}
                                            </x-slot:info>
                                        </x-form.label>
                                    </div>

                                    <x-form.input error-bag="register" name="beta_code" type="text" value="{{ old('beta_code') }}" placeholder="{{ __('Enter your beta code') }}" />
                                </div>
                            @endif

                            <div class="mt-4 bg-[#efefef] rounded-md p-3 flex flex-col gap-y-1 dark:bg-gray-900">
                                <div class="flex gap-x-3 items-center">
                                    <input id="terms" type="checkbox" name="terms" class="rounded mt-1 ring-0 focus:ring-0">
                                    <a href="{{ route('rules.index') }}" target="_blank" class="mt-1 text-sm font-semibold text-gray-700 hover:text-gray-900 hover:underline dark:text-gray-500 dark:hover:text-gray-300">
                                        {{ __('I accept the :hotel terms & rules.', ['hotel' => setting('hotel_name')]) }}
                                    </a>
                                </div>

                                @error('terms')
                                <p class="mt-1 text-xs italic text-red-500">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Used to determine the refer --}}
                            <input type="hidden" name="referral_code" value="{{ $referral_code }}">

                            @if(setting('google_recaptcha_enabled'))
                                <div class="g-recaptcha mt-4" data-sitekey="{{ config('habbo.site.recaptcha_site_key') }}"></div>
                            @endif

                            <div class="mt-4">
                                <x-form.primary-button>
                                    {{ __('Create account') }}
                                </x-form.primary-button>
                            </div>
                        </form>
                    </div>

                    <div class="hidden md:block">
                        <img src="{{ asset('/assets/images/background.png') }}" alt="">
                    </div>
                </div>
            </x-content.content-section>
        </div>
    </div>

    @if(setting('google_recaptcha_enabled'))
        @push('javascript')
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endpush
    @endif
</x-app-layout>
