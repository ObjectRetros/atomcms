@props([
    'title',
    'icon',
    'data',
    'valueKey',
    'valueType',
    'relationship' => null,
    'formatValue' => null,
])

<div class="rounded bg-white p-2 shadow-sm dark:bg-gray-900">
    <div class="flex justify-center gap-x-1 text-center font-semibold text-gray-700 dark:text-gray-300">
        <div class="flex items-center">
            <img src="{{ asset('/assets/images/icons/' . $icon) }}" alt="{{ $title }}" class="w-4" style="image-rendering: pixelated;">
        </div>
        {{ __($title) }}
    </div>
    <hr class="dark:border-gray-500">

    <div class="mt-4 flex flex-col gap-y-3">
        @foreach ($data as $index => $entry)
            <div class="p-3 rounded bg-gray-100 flex gap-x-2 items-center h-[70px] overflow-hidden dark:bg-gray-800">
                <div @class([
                    'w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center',
                    'leaderboard-first' => $index + 1 == 1,
                    'leaderboard-second' => $index + 1 == 2,
                    'leaderboard-third' => $index + 1 == 3,
                ])>
                    {{ $index + 1 }}
                </div>

                <img @class([
                    'mt-8' => !Str::contains(setting('avatar_imager'), 'www.habbo.com'),
                ])
                     src="{{ setting('avatar_imager') }}{{ $relationship ? $entry->{$relationship}?->look : $entry->look }}&size=b&head_direction=2&gesture=sml&headonly=1"
                     alt="" />

                <div class="flex flex-col">
                    <p class="font-bold text-gray-700 dark:text-gray-100">
                        {{ $relationship ? $entry->{$relationship}?->username : $entry->username }}
                    </p>
                    <p class="text-gray-600 dark:text-gray-300">
                        {{ $formatValue ? $formatValue($entry->{$valueKey}) : $entry->{$valueKey} }} {{ $valueType }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>
