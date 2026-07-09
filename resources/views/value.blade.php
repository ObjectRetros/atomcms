<x-app-layout>
    @push('title', __('Rare values'))

    <div class="col-span-12">
        <div class="flex flex-col gap-y-4">
            <a href="{{ route('values.index') }}" class="dark:text-gray-100 underline flex gap-x-1 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                </svg>

                {{ __('Go back to values') }}
            </a>
            <x-content.content-card icon="dragon.png">
                <x-slot:title>
                    {{ $value->name }}
                </x-slot:title>

                <x-slot:under-title>
                    {{ __('Here is a list of all the owned :value`s', ['value' => $value->name]) }}
                </x-slot:under-title>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    @foreach($items as $item)
                        <div class="px-3 h-[100px] rounded bg-gray-200 dark:bg-gray-700 flex gap-4 items-center overflow-hidden">
                            <div class="w-12 h-12 overflow-hidden rounded-full flex items-center justify-center bg-gray-300 dark:bg-gray-800">
                                <img src="{{ sprintf('%s/%s', setting('avatar_imager'), $item['user']->look) }}&headonly=1" alt="">
                            </div>

                            <div class="flex flex-col gap-y-2">
                                <p class="dark:text-gray-100">{{ $item['user']->username }}</p>

                                <div class="w-full bg-yellow-400 rounded h-[35px] flex items-center">
                                    <div class="bg-yellow-500 rounded-l px-2 h-full flex items-center justify-center">
                                        <img class="h-[18px] w-[28px]" src="{{ asset('assets/images/icons/amount.png') }}" alt="">
                                    </div>

                                    <p class="w-full text-center truncate text-sm">
                                        {{ $item['item_count'] ?? 0 }} {{ __('owned') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-content.content-card>
        </div>
    </div>
</x-app-layout>
