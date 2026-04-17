<x-app-layout>
    @push('title', $user->username)

    <div class="col-span-12 flex flex-col gap-y-5">

        {{-- ── PROFILE HEADER ── --}}
        <div class="rounded-xl overflow-hidden profile-bg relative flex items-end md:items-center min-h-[160px]">
            {{-- dark overlay --}}
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent"></div>

            <div class="relative flex flex-col md:flex-row items-start md:items-center gap-x-5 gap-y-2 p-5 w-full">
                {{-- Avatar --}}
                <div class="flex-shrink-0 -mb-1">
                    <img style="image-rendering: pixelated;"
                         class="h-[110px] md:h-[130px] drop-shadow-lg"
                         src="{{ setting('avatar_imager') }}{{ $user->look }}&direction=2&head_direction=3&gesture=sml&action=wav&size=l"
                         alt="{{ $user->username }}">
                </div>

                {{-- Info --}}
                <div class="flex-1 text-white">
                    <p class="text-sm opacity-70">{{ __('My name is,') }}</p>
                    <h2 class="text-3xl font-black leading-tight">{{ $user->username }}</h2>
                    @if($user->motto)
                        <p class="text-sm italic opacity-80 mt-1">"{{ $user->motto }}"</p>
                    @endif
                </div>

                {{-- Currency badges --}}
                <div class="hidden md:flex gap-x-3">
                    <div class="flex flex-col items-center bg-black/30 backdrop-blur-sm rounded-xl px-4 py-2">
                        <img src="{{ asset('/assets/images/profile/credits.png') }}" alt="Credits" class="w-6">
                        <span class="text-[#f8ef2b] font-bold text-lg leading-tight">{{ number_format($user->credits) }}</span>
                        <span class="text-white/60 text-xs">Credits</span>
                    </div>
                    <div class="flex flex-col items-center bg-black/30 backdrop-blur-sm rounded-xl px-4 py-2">
                        <img src="{{ asset('/assets/images/profile/duckets.png') }}" alt="Duckets" class="w-6">
                        <span class="text-[#e99bdc] font-bold text-lg leading-tight">{{ number_format($user->currency('duckets')) }}</span>
                        <span class="text-white/60 text-xs">Duckets</span>
                    </div>
                    <div class="flex flex-col items-center bg-black/30 backdrop-blur-sm rounded-xl px-4 py-2">
                        <img src="{{ asset('/assets/images/profile/diamonds.png') }}" alt="Diamonds" class="w-6">
                        <span class="text-[#82d6db] font-bold text-lg leading-tight">{{ number_format($user->currency('diamonds')) }}</span>
                        <span class="text-white/60 text-xs">Diamonds</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile currency row --}}
        <div class="flex md:hidden gap-x-3">
            <div class="flex-1 flex flex-col items-center bg-[#f8ef2b] rounded-xl py-3">
                <img src="{{ asset('/assets/images/profile/credits.png') }}" alt="" class="w-5">
                <span class="text-[#b16d18] font-bold text-lg">{{ number_format($user->credits) }}</span>
            </div>
            <div class="flex-1 flex flex-col items-center bg-[#e99bdc] rounded-xl py-3">
                <img src="{{ asset('/assets/images/profile/duckets.png') }}" alt="" class="w-5">
                <span class="text-[#812378] font-bold text-lg">{{ number_format($user->currency('duckets')) }}</span>
            </div>
            <div class="flex-1 flex flex-col items-center bg-[#82d6db] rounded-xl py-3">
                <img src="{{ asset('/assets/images/profile/diamonds.png') }}" alt="" class="w-5">
                <span class="text-[#146867] font-bold text-lg">{{ number_format($user->currency('diamonds')) }}</span>
            </div>
        </div>

        {{-- ── TABS ── --}}
        <div x-data="{ tab: 'badges' }">

            {{-- Tab buttons --}}
            <div class="flex gap-x-1 border-b border-gray-200 dark:border-gray-700 mb-5">
                @foreach([
                    ['key' => 'badges',  'label' => __('Badges'),  'icon' => '🏅'],
                    ['key' => 'groups',  'label' => __('Groups'),  'icon' => '🛡️'],
                    ['key' => 'rooms',   'label' => __('Rooms'),   'icon' => '🏠'],
                    ['key' => 'friends', 'label' => __('Friends'), 'icon' => '👥'],
                ] as $t)
                <button
                    @click="tab = '{{ $t['key'] }}'"
                    :class="tab === '{{ $t['key'] }}'
                        ? 'border-b-2 border-[#eeb425] text-gray-900 dark:text-white font-semibold'
                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                    class="px-4 py-2 text-sm flex items-center gap-x-1.5 transition -mb-px">
                    <span>{{ $t['icon'] }}</span>
                    <span>{{ $t['label'] }}</span>
                </button>
                @endforeach
            </div>

            {{-- BADGES --}}
            <div x-show="tab === 'badges'" x-transition>
                @forelse($user->badges as $badge)
                    <div class="inline-flex m-1 h-[70px] w-[70px] border-2 dark:border-gray-700 rounded-full items-center justify-center cursor-pointer bg-white dark:bg-gray-900 shadow"
                         data-tippy-content="{{ $badge->badge_code }}">
                        <img src="{{ setting('badges_path') }}/{{ $badge->badge_code }}.gif"
                             class="max-h-[55px] max-w-[55px]" alt="">
                    </div>
                @empty
                    <p class="text-sm text-gray-400 dark:text-gray-500">
                        {{ __('It seems like :user has no badges.', ['user' => $user->username]) }}
                    </p>
                @endforelse
            </div>

            {{-- GROUPS --}}
            <div x-show="tab === 'groups'" x-transition>
                @forelse($groups as $group)
                    <div class="inline-flex m-1 h-[70px] w-[70px] border-2 dark:border-gray-700 rounded-full overflow-hidden items-center justify-center p-1 cursor-pointer bg-white dark:bg-gray-900 shadow"
                         data-tippy-content="{{ $group->name ?? 'Unknown' }}">
                        <img src="{{ setting('group_badge_path') }}/{{ $group->badge }}.png" alt="">
                    </div>
                @empty
                    <p class="text-sm text-gray-400 dark:text-gray-500">
                        {{ __('It seems like :user is not a member of any groups.', ['user' => $user->username]) }}
                    </p>
                @endforelse
            </div>

            {{-- ROOMS --}}
            <div x-show="tab === 'rooms'" x-transition>
                <div class="flex flex-wrap gap-3">
                    @forelse($user->rooms as $room)
                        <div class="flex flex-col gap-y-1 rounded-lg bg-gray-100 dark:bg-gray-900 p-2 w-[120px] overflow-hidden shadow">
                            <div class="h-[100px] bg-gray-200 dark:bg-gray-800 rounded-md border border-gray-300 dark:border-gray-700 relative flex items-center justify-center">
                                <img src="{{ setting('room_thumbnail_path') }}/{{ $room->id }}.png"
                                     alt="{{ $room->name }}"
                                     class="w-full h-full object-cover rounded-md"
                                     onerror="this.onerror=null;this.src='{{ asset('/assets/images/profile/room_placeholder.png') }}';">
                                <div class="{{ $room->users > 0 ? 'bg-green-600' : 'bg-gray-400' }} absolute bottom-1 right-1 px-1.5 py-0.5 rounded-full flex gap-x-1 text-white items-center text-xs font-bold">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $room->users }}
                                </div>
                            </div>
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 truncate px-1">{{ $room->name }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 dark:text-gray-500">
                            {{ __('It seems like :user got no rooms.', ['user' => $user->username]) }}
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- FRIENDS --}}
            <div x-show="tab === 'friends'" x-transition>
                <div class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 xl:grid-cols-12 gap-2">
                    @forelse($friends as $friend)
                        <a href="{{ route('profile.show', $friend->user->username ?? 'SystemAccount') }}"
                           class="h-[70px] w-[70px] rounded-full border-2 dark:border-gray-700 overflow-hidden flex items-center p-1 cursor-pointer bg-white dark:bg-gray-900 shadow hover:scale-105 transition"
                           data-tippy-content="{{ $friend->user->username ?? 'Unknown' }}">
                            <img class="mt-6" style="image-rendering: pixelated;"
                                 src="{{ setting('avatar_imager') }}?figure={{ $friend->user?->look }}" alt="">
                        </a>
                    @empty
                        <p class="col-span-full text-sm text-gray-400 dark:text-gray-500">
                            {{ __('It seems like :user has no friends.', ['user' => $user->username]) }}
                        </p>
                    @endforelse
                </div>
            </div>

        </div>
        {{-- end tabs --}}

    </div>

    @push('javascript')
    <script type="module">
        tippy('[data-tippy-content]');
    </script>
    @endpush
</x-app-layout>
