<x-app-layout>
    @push('title', __('Forgot password'))

    <div class="col-span-12">
        <x-content.content-card icon="hotel-icon" classes="max-w-[640px] mx-auto">
            <x-slot:title>
                {{ __('Did you forget your password?') }}
            </x-slot:title>

            <x-slot:under-title>
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </x-slot:under-title>

            <form method="POST" action="{{ route('forgot.password.post') }}">
                @csrf

                 <!-- Email Address -->
                 <div>
                    <div class="flex flex-col gap-y-2">
                        <x-form.label for="mail">
                            {{ __('Email') }}

                            <x-slot:info>
                                {{ __('Enter your email') }}
                            </x-slot:info>
                        </x-form.label>
                    </div>

                    <x-form.input name="mail" type="email" placeholder="{{ __('Enter your email') }}"/>
                </div>

                <div class="mt-4">
                    <x-form.primary-button>
                        {{ __('Email Password Reset Link') }}
                    </x-form.primary-button>
                </div>
            </form>
        </x-content.content-card>
    </div>
</x-app-layout>
