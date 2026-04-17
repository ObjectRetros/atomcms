<x-app-layout>
    @push('title', __('Banned'))
    <div class="col-span-12 flex justify-center">
        <div class="space-y-4 lg:w-1/2">
            <div class="w-full rounded-md bg-red-500 text-white text-center p-2">
                {{ __('It seems like you are banned off :hotel', ['hotel' => setting('hotel_name')]) }}
            </div>

            <div class="shadow rounded-md p-2">
                <div class="flex justify-between">
                    <div class="flex flex-col px-1">
                        <div class="max-w-[380px]">
                            <p>
                                <strong>{{ __('Ban type:') }}</strong> {{ $ban->type }}
                            </p>

                            <p>
                                <strong>{{ __('Ban reason:') }}</strong> {{ $ban->ban_reason }}
                            </p>

                            <p>
                                <strong>Ban expiration:</strong> {{ date('Y/m/d', $ban->ban_expire) }}
                            </p>
                        </div>

                        <div class="mt-4 max-w-[380px]">
                            <p class="mb-4">
                                {{ __('If you believe this is a mistake, please reach out to one of our staff members through our Discord server!') }}
                            </p>

                            <a href="https://discord.gg/7jftX65hYV" target="_blank">
                                <x-form.primary-button>
                                    {{ __('Join discord') }}
                                </x-form.primary-button>
                            </a>
                        </div>
                    </div>

                    <div class="hidden mr-8 lg:flex items-center">
                        <img src="{{ asset('assets/images/angry_frank.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
