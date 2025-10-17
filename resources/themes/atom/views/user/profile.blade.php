<x-app-layout>
    @push('title', $user->username)

    <div class="col-span-12">
        <div class="grid grid-cols-1! gap-y-14">
            <div class="grid grid-cols-3! gap-x-8">
                <div
                    class="col-span-3 md:col-span-1! h-[150px] lg:h-[220px] profile-bg rounded-lg relative flex gap-x-2 items-center text-white overflow-hidden">
                    <img class="mt-14 drop-shadow lg:mt-0" style="image-rendering: pixelated;"
                        src="{{ setting('avatar_imager') }}{{ $user->look }}&direction=2&head_direction=3&gesture=sml&action=wav&size=l"
                        alt="">

                    <div class="flex flex-col">
                        <h3 class="text-xl font-semibold">{{ __('My name is,') }}</h3>
                        <h2 class="text-4xl">
                            {{ $user->username }}
                        </h2>

                        <h4 class="text-lg font-semibold italic">{{ $user->motto }}</h4>
                    </div>
                </div>

                <div
                    class="col-span-3! mt-4 grid w-full grid-cols-1 space-y-3 md:space-y-0 md:col-span-2! md:mt-0 md:grid-cols-3">
                    <div
                        class="rounded-lg md:rounded-none md:rounded-l-lg bg-[#f8ef2b] flex flex-col gap-y-2 items-center justify-center py-3 md:py-0">
                        <img src="{{ asset('/assets/images/profile/credits.png') }}" alt="">

                        <h4 class="text-[#b16d18] font-semibold text-2xl">
                            {{ $user->credits }}
                        </h4>
                    </div>

                    <div
                        class="rounded-lg md:rounded-none bg-[#e99bdc] flex flex-col gap-y-2 items-center justify-center py-3 md:py-0">
                        <img src="{{ asset('/assets/images/profile/duckets.png') }}" alt="">

                        <h4 class="text-[#812378] font-semibold text-2xl">
                            {{ $user->currency('duckets') }}
                        </h4>
                    </div>

                    <div
                        class="rounded-lg md:rounded-none md:rounded-r-lg! bg-[#82d6db] flex flex-col gap-y-2 items-center justify-center py-3 md:py-0">
                        <img src="{{ asset('/assets/images/profile/diamonds.png') }}" alt="">

                        <h4 class="text-[#146867] font-semibold text-2xl">
                            {{ $user->currency('diamonds') }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="hidden grid-cols-2 gap-x-14 md:grid">
                <div class="col-span-1">
                    <x-user.profile-info-card col-span="1">
                        <x-slot:image>
                            <img src="{{ asset('/assets/images/profile/badges.png') }}" alt="">
                        </x-slot:image>

                        <x-slot:title>
                            {{ __('Badges') }}
                        </x-slot:title>

                        <div class="flex justify-between">
                            @forelse($user->badges as $badge)
                                <div data-tippy-content="{{ $badge->badge_code }}"
                                    class="user-badge h-[70px] w-[70px] border-2 dark:border-gray-700 rounded-full flex items-center justify-center cursor-pointer">
                                    <img src="{{ setting('badges_path') }}/{{ $badge->badge_code }}.gif"
                                        class="max-h-[55px] max-w-[55px]" alt="">
                                </div>
                            @empty
                                <div class="col-span-4">
                                    {{ __('It seems like :user has no badges.', ['user' => $user->username]) }}
                                </div>
                            @endforelse
                        </div>
                    </x-user.profile-info-card>
                </div>

                <div class="col-span-1">
                    <x-user.profile-info-card col-span="1">
                        <x-slot:image>
                            <img src="{{ asset('/assets/images/profile/groups.png') }}" alt="">
                        </x-slot:image>

                        <x-slot:title>
                            {{ __('Groups') }}
                        </x-slot:title>

                        <div class="flex justify-between">
                            @forelse($groups as $group)
                                <div class="h-[70px] w-[70px] rounded-full border-2 dark:border-gray-700 overflow-hidden flex items-center justify-center p-1 rounded-md cursor-pointer friend"
                                    data-tippy-content="{{ $group->name ?? 'Unknown' }}">
                                    <img src="{{ setting('group_badge_path') }}/{{ $group->badge }}.png"
                                        alt="">
                                </div>
                            @empty
                                <div class="w-full">
                                    {{ __('It seems like :user is not a member of any groups.', ['user' => $user->username]) }}

                                </div>
                            @endforelse
                        </div>
                    </x-user.profile-info-card>
                </div>
            </div>

            <div class="hidden grid-cols-2 gap-x-14 md:grid">
                <div class="col-span-1">
                    <x-user.profile-info-card col-span="1">
                        <x-slot:image>
                            <img src="{{ asset('/assets/images/profile/rooms.png') }}" alt="">
                        </x-slot:image>

                        <x-slot:title>
                            {{ __('Rooms') }}
                        </x-slot:title>

                        <div class="flex space-x-1 justify-between">
                            @forelse($user->rooms as $room)
                                <div
                                    class="flex w-[120px] flex-col gap-y-1 rounded-md dark:bg-gray-900 bg-gray-200 p-1 overflow-hidden">
                                    <div class="h-full inline-block">
                                        <div class="h-full relative border border-gray-500 dark:border-gray-700 rounded flex items-center justify-center flex-col">
                                            <img
                                                class="rounded"
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

                                    <div class="flex items-center justify-between px-1">
                                        <p class="truncate">{{ Str::limit($room->name, 6) }}</p>

                                        <a href="#">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 text-cyan-300 hover:text-cyan-400"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-4">
                                    {{ __('It seems like :user got no rooms.', ['user' => $user->username]) }}
                                </div>
                            @endforelse
                        </div>
                    </x-user.profile-info-card>
                </div>

                <div class="col-span-1">
                    <x-user.profile-info-card col-span="1">
                        <x-slot:image>
                            <img src="{{ asset('/assets/images/profile/friends.png') }}" alt="">
                        </x-slot:image>

                        <x-slot:title>
                            {{ __('Friends') }}
                        </x-slot:title>

                        <div class="grid grid-cols-4 gap-2 xl:grid-cols-6 xl:pl-3">
                            @forelse($friends as $friend)
                                <a href="{{ route('profile.show', $friend->user->username ?? 'SystemAccount') }}"
                                    class="h-[70px] w-[70px] rounded-full border-2 dark:border-gray-700 overflow-hidden flex items-center p-1 rounded-md cursor-pointer friend"
                                    data-tippy-content="{{ $friend->user->username ?? 'Unknown' }}">
                                    <img class="mt-6 transition duration-200 ease-in-out hover:scale-110"
                                        src="{{ setting('avatar_imager') }}{{ $friend->user?->look }}"
                                        alt="">
                                </a>
                            @empty
                                <div class="col-span-6">
                                    {{ __('It seems like :user has no friends.', ['user' => $user->username]) }}
                                </div>
                            @endforelse
                        </div>
                    </x-user.profile-info-card>
                </div>
            </div>
        </div>
    </div>

    @push('javascript')
        <script type="module">
            tippy('.user-badge');
            tippy('.friend');
            tippy('.group');
        </script>
    @endpush
</x-app-layout>
