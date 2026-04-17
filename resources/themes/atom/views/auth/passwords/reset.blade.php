<x-app-layout>
    @push('title', __('Set New Password'))

    <div class="col-span-12">
        <div class="lg:px-[250px]">
            <x-content.content-section icon="hotel-icon" classes="flex flex-col">
                <x-slot:title>
                    {{ __('Set a new password') }}
                </x-slot:title>

                <x-slot:under-title>
                    {{ __('Choose a strong password for your account.') }}
                </x-slot:under-title>

                <x-messages.flash-messages />

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <x-form.label for="email">{{ __('Email') }}</x-form.label>
                        <x-form.input error-bag="default" name="email" type="email"
                            value="{{ old('email', $request->email) }}" placeholder="{{ __('your@email.com') }}" :autofocus="true"/>
                    </div>

                    <div class="mt-4">
                        <x-form.label for="password">{{ __('New Password') }}</x-form.label>
                        <x-form.input error-bag="default" name="password" type="password"
                            placeholder="{{ __('New password') }}"/>
                    </div>

                    <div class="mt-4">
                        <x-form.label for="password_confirmation">{{ __('Confirm Password') }}</x-form.label>
                        <x-form.input error-bag="default" name="password_confirmation" type="password"
                            placeholder="{{ __('Repeat new password') }}"/>
                    </div>

                    <div class="mt-4">
                        <x-form.primary-button>
                            {{ __('Reset Password') }}
                        </x-form.primary-button>
                    </div>
                </form>
            </x-content.content-section>
        </div>
    </div>
</x-app-layout>
