<x-app-layout>
    @push('title', __('Başarımlar'))

    <div class="col-span-12">

        {{-- Header Stats --}}
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

            {{-- Main: Categories --}}
            <div class="flex-1 flex flex-col gap-5">

                @foreach($categories as $key => $cat)
                @php
                    $catPct = $cat['total'] > 0 ? round($cat['earned'] / $cat['total'] * 100) : 0;
                @endphp
                <div class="shadow rounded bg-white dark:bg-gray-900 overflow-hidden">

                    {{-- Category header --}}
                    <div class="px-4 py-3 flex items-center justify-between border-b dark:border-gray-700 cursor-pointer"
                         onclick="toggleCategory('cat-{{ $key }}')">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xl">{{ $cat['icon'] }}</span>
                            <span class="font-bold text-gray-700 dark:text-gray-200">{{ $cat['label'] }}</span>
                            <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full">
                                {{ $cat['earned'] }} / {{ $cat['total'] }}
                            </span>
                        </div>
                        <div class="flex items-center gap-x-3">
                            <div class="hidden sm:flex items-center gap-x-2">
                                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-500
                                        @if($catPct == 100) bg-green-500
                                        @elseif($catPct >= 50) bg-yellow-500
                                        @else bg-blue-400 @endif"
                                         style="width: {{ $catPct }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 w-8">{{ $catPct }}%</span>
                            </div>
                            <svg id="chevron-{{ $key }}" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Achievements grid --}}
                    <div id="cat-{{ $key }}" class="p-4">
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

                                {{-- Badge icon --}}
                                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-lg
                                    @if($earned) bg-yellow-400 dark:bg-yellow-600
                                    @else bg-gray-200 dark:bg-gray-700 @endif">
                                    @if($earned) 🏅 @else 🔒 @endif
                                </div>

                                {{-- Info --}}
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

                                    {{-- Progress bar --}}
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
                </div>
                @endforeach

            </div>

            {{-- Sidebar: Top Earners --}}
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

                {{-- My quick stats --}}
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

    @push('scripts')
    <script>
        function toggleCategory(id) {
            const el = document.getElementById(id);
            const key = id.replace('cat-', '');
            const chevron = document.getElementById('chevron-' + key);
            el.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }
    </script>
    @endpush

</x-app-layout>
