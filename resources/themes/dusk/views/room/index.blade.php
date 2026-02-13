<x-app-layout>
    @push('title', $room->name)

    <div class="col-span-12">
        <div class="grid grid-cols-12 gap-y-4 gap-x-2 lg:gap-x-4">
            <div class="col-span-12 md:col-span-8 sm:flex gap-x-4">
                <div class="inline-block">
                    <div class="relative">
                        <img
                            class="min-h-[110px] min-w-[110px] rounded"
                            src="{{ setting('room_thumbnail_path') }}/{{ $room->id }}.png"
                            alt="{{ $room->name }}"
                            onerror="this.onerror=null;this.src='{{ asset('/assets/images/profile/room_placeholder.png') }}';"
                        >

                        <div class="absolute bottom-1 left-1/2 transform -translate-x-1/2">
                            <div class="{{ $room->users > 0 ? 'bg-[#00800B]' : 'bg-gray-400' }} px-1 py-[1px] font-semibold rounded flex gap-x-[3px] text-white items-center text-xs">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-[12px]" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd"
                                    />
                                </svg>

                                {{ $room->users }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dark:text-white">
                    <h5 class="text-xl font-bold">{{ $room->name }}</h5>
                    <a class="flex items-center" href="/profile/{{ $room->owner->username }}">
                        <img class="h-12" src="{{ setting('avatar_imager') }}{{ $room->owner->look }}&direction=2&headonly=1&head_direction=2&gesture=sml" alt="{{ $room->owner->username }}">
                        <p>{{ $room->owner?->username ?? 'Unknown' }}</p>
                    </a>
                    <p class="leading-5">
                        <span class="font-semibold">{{ __('Description:') }} </span>
                        {{ $room->description }}
                    </p>
                </div>
            </div>

            <div class="col-span-12 md:col-span-4">
                <div class="grid grid-cols-1 gap-y-2">
                    <div class="shadow border dark:border-gray-900">
                        <div class="flex gap-x-2 rounded-t border-b bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900">
                            <p class="font-semibold text-black dark:text-white">
                                {{ __('Room details') }}
                            </p>
                        </div>

                        <section class="rounded-b bg-white p-3 dark:bg-gray-800 dark:text-white">
                            <p>
                                <span class="font-semibold">{{ __('Max users:') }} </span>
                                {{ $room->users_max }}
                            </p>
                            @if (strlen($room->tags) > 0)
                                <p>
                                    <span class="font-semibold">Tags: </span>
                                    @foreach (explode(";", $room->tags) as $tag)
                                        @if (empty($tag) === false)
                                            <span class="rounded bg-gray-200 dark:bg-gray-700 px-2">{{ $tag }}</span>
                                        @endif
                                    @endforeach
                                </p>
                            @endif
                        </section>
                    </div>
                    @if ($room->guild !== null)
                        <x-content.content-card icon="{{ $room->guild->badge }}-icon" classes="border dark:border-gray-900">
                            <x-slot:title>
                                {{ __('The room guild') }}
                            </x-slot:title>

                            <x-slot:under-title>
                                {{ $room->guild->name }}
                            </x-slot:under-title>

                            <p class="text-[14px] dark:text-gray-300">
                                {{ $room->guild->description }}
                            </p>
                        </x-content.content-card>
                        <style>
                            .{{ $room->guild->badge }}-icon {
                                background: #f68b08 url("{{ setting('group_badge_path') }}/{{ $room->guild->badge }}.png") no-repeat center;
                            }
                        </style>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
