<x-app-layout>
    @push('title', __('Password settings'))

    <div class="col-span-12 flex flex-col gap-y-0">
        <x-user.settings.settings-navigation />

        <x-content.content-section icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Password settings') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Change your password by filling out the fields below') }}
            </x-slot:under-title>

            <form action="{{ route('settings.password.update') }}" method="POST" class="flex flex-col gap-y-4 max-w-lg">
                @method('PUT')
                @csrf

                <div class="flex flex-col gap-y-1">
                    <x-form.label for="current_password">
                        {{ __('Current password') }}
                        <x-slot:info>
                            {{ __('Enter your current password') }}
                        </x-slot:info>
                    </x-form.label>
                    <x-form.input name="current_password" type="password" :autofocus="true" />
                </div>

                <div class="flex flex-col gap-y-1">
                    <x-form.label for="password">
                        {{ __('New password') }}
                        <x-slot:info>
                            {{ __('Enter a new secure password. Do not forget to save it somewhere safe') }}
                        </x-slot:info>
                    </x-form.label>
                    <x-form.input name="password" type="password" />
                </div>

                <div class="flex flex-col gap-y-1">
                    <x-form.label for="password_confirmation">
                        {{ __('Confirm new password') }}
                        <x-slot:info>
                            {{ __('Please confirm your new password') }}
                        </x-slot:info>
                    </x-form.label>
                    <x-form.input name="password_confirmation" type="password" />
                </div>

                <div>
                    <x-form.secondary-button>
                        {{ __('Update password') }}
                    </x-form.secondary-button>
                </div>
            </form>
        </x-content.content-section>
    </div>
</x-app-layout>
