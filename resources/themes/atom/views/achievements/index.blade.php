<x-app-layout>
    @push('title', __('Başarımlar'))

    <div class="col-span-12">

        {{-- ── STATS HEADER ── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="p-4 shadow rounded bg-white dark:bg-gray-900 text-center">
                <div class="text-3xl font-black text-yellow-500">{{ $earnedCount }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tamamlanan</div>
                <div class="text-xs text-gray-400 dark:text-gray-500">/ {{ $totalAchievements }} başarım</div>
            </div>
            <div class="p-4 shadow rounded bg-white dark:bg-gray-900 text-center">
                <div class="text-3xl font-black text-blue-500">{{ number_format($achievementScore) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Başarım Skoru</div>
            </div>
            <div class="p-4 shadow rounded bg-white dark:bg-gray-900 text-center">
                <div class="text-3xl font-black text-green-500">{{ number_format($earnedPoints) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kazanılan Puan</div>
                <div class="text-xs text-gray-400 dark:text-gray-500">/ {{ number_format($totalPoints) }} puan</div>
            </div>
            <div class="p-4 shadow rounded bg-white dark:bg-gray-900 text-center">
                @php $pct = $totalAchievements > 0 ? round($earnedCount / $totalAchievements * 100) : 0; @endphp
                <div class="text-3xl font-black text-purple-500">{{ $pct }}%</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tamamlanma</div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-2">
                    <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">

            {{-- ── MAIN: CATEGORY TABS ── --}}
            <div class="flex-1" x-data="{ tab: 0 }">

                {{-- Tab bar --}}
                <div class="flex flex-nowrap overflow-x-auto gap-x-0 border-b border-gray-200 dark:border-gray-700 mb-5">
                    @foreach($categories as $key => $cat)
                    @php $catPct = $cat['total'] > 0 ? round($cat['earned'] / $cat['total'] * 100) : 0; @endphp
                    <button
                        @click="tab = {{ $loop->index }}"
                        :class="tab === {{ $loop->index }}
                            ? 'border-b-2 border-[#eeb425] text-gray-900 dark:text-white font-semibold'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                        class="flex-shrink-0 px-3 py-2 text-sm flex items-center gap-x-1.5 transition -mb-px whitespace-nowrap">
                        <span>{{ $cat['icon'] }}</span>
                        <span class="hidden sm:inline">{{ $cat['label'] }}</span>
                        <span class="text-xs opacity-60">{{ $cat['earned'] }}/{{ $cat['total'] }}</span>
                    </button>
                    @endforeach
                </div>

                {{-- Tab panels --}}
                @foreach($categories as $key => $cat)
                @php
                    $catPct2 = $cat['total'] > 0 ? round($cat['earned'] / $cat['total'] * 100) : 0;
                @endphp
                <div x-show="tab === {{ $loop->index }}"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0">

                    {{-- Category header bar --}}
                    <div class="flex items-center gap-x-3 mb-4 px-1">
                        <span class="text-2xl">{{ $cat['icon'] }}</span>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-gray-700 dark:text-gray-200">{{ $cat['label'] }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $cat['earned'] }} / {{ $cat['total'] }} · {{ $catPct2 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500
                                    @if($catPct2 == 100) bg-green-500
                                    @elseif($catPct2 >= 50) bg-yellow-500
                                    @else bg-blue-400 @endif"
                                     style="width: {{ $catPct2 }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Achievements grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                        @foreach($cat['items'] as $ach)
                        @php
                            $earned = $ach->user_progress >= $ach->progress_needed;
                            $progress = $ach->progress_needed > 0
                                ? min(100, round($ach->user_progress / $ach->progress_needed * 100))
                                : 0;
                        @endphp
                        <div class="flex items-start gap-x-3 p-3 rounded-lg border
                            @if($earned) border-yellow-300 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-700
                            @else border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 @endif">

                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-lg
                                @if($earned) bg-yellow-400 dark:bg-yellow-600
                                @else bg-gray-200 dark:bg-gray-700 @endif">
                                @if($earned) 🏅 @else 🔒 @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-x-1">
                                    <p class="text-sm font-semibold truncate
                                        @if($earned) text-yellow-800 dark:text-yellow-300
                                        @else text-gray-600 dark:text-gray-400 @endif">
                                        {{ str_replace('ACH_', '', $ach->name) }}
                                        @if($ach->level > 1)
                                            <span class="text-xs font-normal opacity-60">Lv.{{ $ach->level }}</span>
                                        @endif
                                    </p>
                                    @if($ach->points)
                                    <span class="flex-shrink-0 text-xs font-bold
                                        @if($earned) text-yellow-600 dark:text-yellow-400
                                        @else text-gray-400 @endif">
                                        +{{ $ach->points }}p
                                    </span>
                                    @endif
                                </div>

                                @if(!$earned)
                                <div class="mt-1.5">
                                    <div class="flex justify-between text-xs text-gray-400 mb-0.5">
                                        <span>{{ $ach->user_progress }} / {{ $ach->progress_needed }}</span>
                                        <span>{{ $progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                        <div class="bg-blue-400 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                                @else
                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">✓ Tamamlandı</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

            </div>

            {{-- ── SIDEBAR ── --}}
            <div class="lg:w-64 flex flex-col gap-4">
                <div class="p-4 shadow rounded bg-white dark:bg-gray-900">
                    <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-3 flex items-center gap-x-2">
                        🏆 <span>En Yüksek Skor</span>
                    </h3>
                    <div class="flex flex-col gap-y-3">
                        @foreach($topEarners as $index => $earner)
                        <div class="flex items-center gap-x-2 p-2 rounded
                            @if($earner->username === auth()->user()->username) bg-yellow-50 dark:bg-yellow-900/20 @else bg-gray-50 dark:bg-gray-800 @endif">
                            <div @class([
                                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold',
                                'leaderboard-first'  => $index === 0,
                                'leaderboard-second' => $index === 1,
                                'leaderboard-third'  => $index === 2,
                                'bg-gray-200 dark:bg-gray-700 text-gray-500' => $index > 2,
                            ])>{{ $index + 1 }}</div>
                            <img src="{{ setting('avatar_imager') }}{{ $earner->look }}&size=b&head_direction=2&gesture=sml&headonly=1"
                                 class="w-8 h-8 @if(!Str::contains(setting('avatar_imager'), 'www.habbo.com')) mt-2 @endif"
                                 alt="{{ $earner->username }}" />
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-gray-700 dark:text-gray-200 truncate">{{ $earner->username }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($earner->achievement_score) }} puan</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 shadow rounded bg-white dark:bg-gray-900">
                    <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-3 flex items-center gap-x-2">
                        📊 <span>Kategorilere Göre</span>
                    </h3>
                    <div class="flex flex-col gap-y-2">
                        @foreach($categories as $key => $cat)
                        @php $cp = $cat['total'] > 0 ? round($cat['earned'] / $cat['total'] * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-0.5">
                                <span>{{ $cat['icon'] }} {{ $cat['label'] }}</span>
                                <span>{{ $cat['earned'] }}/{{ $cat['total'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full
                                    @if($cp == 100) bg-green-500
                                    @elseif($cp >= 50) bg-yellow-500
                                    @else bg-blue-400 @endif"
                                     style="width: {{ $cp }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
