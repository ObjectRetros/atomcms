<x-app-layout>
    @push('title', __('Verify Email'))

    <div class="col-span-12">
        <div class="lg:px-[250px]">
            <x-content.content-section icon="hotel-icon" classes="flex flex-col">
                <x-slot:title>
                    {{ __('Verify your email address') }}
                </x-slot:title>

                <x-slot:under-title>
                    {{ __('One last step before you can start playing!') }}
                </x-slot:under-title>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 p-3 rounded bg-green-100 dark:bg-green-900/30 text-sm font-medium text-green-700 dark:text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </div>
                @endif

                <div class="mb-5 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                    <p>{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?') }}</p>
                    <p>{{ __('If you didn\'t receive the email, we will gladly send you another.') }}</p>
                </div>

                <div class="flex items-center justify-between gap-x-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <x-form.primary-button>
                            {{ __('Resend Verification Email') }}
                        </x-form.primary-button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </x-content.content-section>
        </div>
    </div>
</x-app-layout>
