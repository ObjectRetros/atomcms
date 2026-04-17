<x-app-layout>
    @push('title', __('Reset Password'))

    <div class="col-span-12">
        <div class="lg:px-[250px]">
            <x-content.content-section icon="hotel-icon" classes="flex flex-col">
                <x-slot:title>
                    {{ __('Forgot your password?') }}
                </x-slot:title>

                <x-slot:under-title>
                    {{ __('Enter your email address and we\'ll send you a reset link.') }}
                </x-slot:under-title>

                @if (session('status'))
                    <div class="mb-4 p-3 rounded bg-green-100 dark:bg-green-900/30 text-sm font-medium text-green-700 dark:text-green-400">
                        {{ session('status') }}
                    </div>
                @endif

                <x-messages.flash-messages />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div>
                        <x-form.label for="email">{{ __('Email') }}</x-form.label>
                        <x-form.input error-bag="default" name="email" type="email"
                            value="{{ old('email') }}" placeholder="{{ __('your@email.com') }}" :autofocus="true"/>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <a href="{{ route('login') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">
                            {{ __('Back to login') }}
                        </a>
                        <x-form.primary-button>
                            {{ __('Send Reset Link') }}
                        </x-form.primary-button>
                    </div>
                </form>
            </x-content.content-section>
        </div>
    </div>
</x-app-layout>
