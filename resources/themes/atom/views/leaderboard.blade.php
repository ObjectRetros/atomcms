<x-app-layout>
    @push('title', __('Leaderboard'))

    <div class="col-span-12" x-data="{ tab: 'credits' }">

        {{-- ── TAB BAR ── --}}
        <div class="flex flex-nowrap overflow-x-auto gap-x-0 border-b border-gray-200 dark:border-gray-700 mb-6">
            @foreach([
                ['key' => 'credits',  'label' => __('Top Credits'),     'icon' => 'credits.png'],
                ['key' => 'duckets',  'label' => __('Top Duckets'),     'icon' => 'duckets.png'],
                ['key' => 'diamonds', 'label' => __('Top Diamonds'),    'icon' => 'diamond.png'],
                ['key' => 'online',   'label' => __('Hours Online'),    'icon' => 'clock.gif'],
                ['key' => 'respects', 'label' => __('Respects'),        'icon' => 'heart.gif'],
                ['key' => 'score',    'label' => __('Achievement'),     'icon' => 'star.gif'],
            ] as $t)
            <button
                @click="tab = '{{ $t['key'] }}'"
                :class="tab === '{{ $t['key'] }}'
                    ? 'border-b-2 border-[#eeb425] text-gray-900 dark:text-white font-semibold'
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                class="flex-shrink-0 px-4 py-2 text-sm flex items-center gap-x-2 transition -mb-px whitespace-nowrap">
                <img src="{{ asset('/assets/images/icons/' . $t['icon']) }}"
                     class="w-4 h-4" style="image-rendering: pixelated">
                {{ $t['label'] }}
            </button>
            @endforeach
        </div>

        @php
            $rankCls = fn(int $i): string => match($i) {
                0 => 'leaderboard-first',
                1 => 'leaderboard-second',
                2 => 'leaderboard-third',
                default => 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
            };
            $avatarCls = !Str::contains(setting('avatar_imager'), 'www.habbo.com') ? 'mt-8' : '';
        @endphp

        <div class="max-w-lg mx-auto flex flex-col gap-y-3">

            {{-- CREDITS --}}
            <template x-if="tab === 'credits'">
                <div class="flex flex-col gap-y-3" x-transition>
                    @foreach($credits as $index => $user)
                    <div class="p-3 rounded-lg bg-white dark:bg-gray-900 shadow flex gap-x-3 items-center h-[70px] overflow-hidden">
                        <div class="{{ $rankCls($index) }} w-10 h-10 rounded-full flex items-center justify-center font-bold flex-shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <img src="{{ setting('avatar_imager') }}{{ $user->look }}&size=b&head_direction=2&gesture=sml&headonly=1"
                             class="{{ $avatarCls }}" alt="{{ $user->username }}" />
                        <div class="flex flex-col min-w-0">
                            <a href="{{ route('profile.show', $user->username) }}"
                               class="font-bold text-gray-700 dark:text-gray-100 truncate hover:underline">
                                {{ $user->username }}
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __(':credits credits', ['credits' => number_format($user->credits)]) }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </template>

            {{-- DUCKETS --}}
            <template x-if="tab === 'duckets'">
                <div class="flex flex-col gap-y-3" x-transition>
                    @foreach($duckets as $index => $currency)
                    <div class="p-3 rounded-lg bg-white dark:bg-gray-900 shadow flex gap-x-3 items-center h-[70px] overflow-hidden">
                        <div class="{{ $rankCls($index) }} w-10 h-10 rounded-full flex items-center justify-center font-bold flex-shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <img src="{{ setting('avatar_imager') }}{{ $currency->user?->look }}&size=b&head_direction=2&gesture=sml&headonly=1"
                             class="{{ $avatarCls }}" alt="" />
                        <div class="flex flex-col min-w-0">
                            <a href="{{ route('profile.show', $currency->user?->username ?? 'SystemAccount') }}"
                               class="font-bold text-gray-700 dark:text-gray-100 truncate hover:underline">
                                {{ $currency->user?->username }}
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __(':duckets duckets', ['duckets' => number_format($currency->amount)]) }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </template>

            {{-- DIAMONDS --}}
            <template x-if="tab === 'diamonds'">
                <div class="flex flex-col gap-y-3" x-transition>
                    @foreach($diamonds as $index => $currency)
                    <div class="p-3 rounded-lg bg-white dark:bg-gray-900 shadow flex gap-x-3 items-center h-[70px] overflow-hidden">
                        <div class="{{ $rankCls($index) }} w-10 h-10 rounded-full flex items-center justify-center font-bold flex-shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <img src="{{ setting('avatar_imager') }}{{ $currency->user?->look }}&size=b&head_direction=2&gesture=sml&headonly=1"
                             class="{{ $avatarCls }}" alt="" />
                        <div class="flex flex-col min-w-0">
                            <a href="{{ route('profile.show', $currency->user?->username ?? 'SystemAccount') }}"
                               class="font-bold text-gray-700 dark:text-gray-100 truncate hover:underline">
                                {{ $currency->user?->username }}
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __(':diamonds diamonds', ['diamonds' => number_format($currency->amount)]) }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </template>

            {{-- HOURS ONLINE --}}
            <template x-if="tab === 'online'">
                <div class="flex flex-col gap-y-3" x-transition>
                    @foreach($mostOnline as $index => $onlineTime)
                    <div class="p-3 rounded-lg bg-white dark:bg-gray-900 shadow flex gap-x-3 items-center h-[70px] overflow-hidden">
                        <div class="{{ $rankCls($index) }} w-10 h-10 rounded-full flex items-center justify-center font-bold flex-shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <img src="{{ setting('avatar_imager') }}{{ $onlineTime->user?->look }}&size=b&head_direction=2&gesture=sml&headonly=1"
                             class="{{ $avatarCls }}" alt="" />
                        <div class="flex flex-col min-w-0">
                            <a href="{{ route('profile.show', $onlineTime->user?->username ?? 'SystemAccount') }}"
                               class="font-bold text-gray-700 dark:text-gray-100 truncate hover:underline">
                                {{ $onlineTime->user?->username }}
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __(':online hours', ['online' => number_format(round($onlineTime->online_time / 3600))]) }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </template>

            {{-- RESPECTS --}}
            <template x-if="tab === 'respects'">
                <div class="flex flex-col gap-y-3" x-transition>
                    @foreach($respectsReceived as $index => $respect)
                    <div class="p-3 rounded-lg bg-white dark:bg-gray-900 shadow flex gap-x-3 items-center h-[70px] overflow-hidden">
                        <div class="{{ $rankCls($index) }} w-10 h-10 rounded-full flex items-center justify-center font-bold flex-shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <img src="{{ setting('avatar_imager') }}{{ $respect->user?->look }}&size=b&head_direction=2&gesture=sml&headonly=1"
                             class="{{ $avatarCls }}" alt="" />
                        <div class="flex flex-col min-w-0">
                            <a href="{{ route('profile.show', $respect->user?->username ?? 'SystemAccount') }}"
                               class="font-bold text-gray-700 dark:text-gray-100 truncate hover:underline">
                                {{ $respect->user?->username }}
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __(':respect respects received', ['respect' => number_format($respect->respects_received)]) }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </template>

            {{-- ACHIEVEMENT SCORE --}}
            <template x-if="tab === 'score'">
                <div class="flex flex-col gap-y-3" x-transition>
                    @foreach($achievementScores as $index => $achievement)
                    <div class="p-3 rounded-lg bg-white dark:bg-gray-900 shadow flex gap-x-3 items-center h-[70px] overflow-hidden">
                        <div class="{{ $rankCls($index) }} w-10 h-10 rounded-full flex items-center justify-center font-bold flex-shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <img src="{{ setting('avatar_imager') }}{{ $achievement->user?->look }}&size=b&head_direction=2&gesture=sml&headonly=1"
                             class="{{ $avatarCls }}" alt="" />
                        <div class="flex flex-col min-w-0">
                            <a href="{{ route('profile.show', $achievement->user?->username ?? 'SystemAccount') }}"
                               class="font-bold text-gray-700 dark:text-gray-100 truncate hover:underline">
                                {{ $achievement->user?->username }}
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __(':achievement achievement score', ['achievement' => number_format($achievement->achievement_score)]) }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </template>

        </div>
    </div>
</x-app-layout>