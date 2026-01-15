
@props(['iconUrl' => '', 'color' => '', 'classes' => ''])

<div class="w-full flex flex-col gap-y-4 rounded overflow-hidden bg-white pb-3 dark:bg-gray-800 shadow-sm {{ $classes }}">
    <div class="flex gap-x-2 border-b bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900">
        @if (!empty($iconUrl))
            <div style="background-image: url({{ $iconUrl }}); background-color: {{ $color }}; background-repeat: no-repeat; background-position: center;" class="max-w-[50px] max-h-[50px] min-w-[50px] min-h-[50px] rounded-full relative flex items-center justify-center"></div>
        @endif

        <div class="flex flex-col justify-center text-sm">
            <p class="font-semibold text-black dark:text-gray-300">{{ $title }}</p>

            @if(isset($underTitle))
                <p class="dark:text-gray-500">{{ $underTitle }}</p>
            @endif
        </div>
    </div>

    <section class="h-full flex flex-col px-3">
        {{ $slot }}
    </section>
</div>
