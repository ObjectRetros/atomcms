
@props(['iconUrl' => '', 'color' => '', 'classes' => ''])

<div class="w-full flex flex-col gap-y-4 rounded overflow-hidden bg-[#2b303c] pb-3 shadow-sm text-gray-100 {{ $classes }}">
    <div class="flex gap-x-2 bg-[#21242e] p-3 text-gray-100">
        @if (!empty($iconUrl))
            <div style="background-image: url({{ $iconUrl }}); background-color: {{ $color }}; background-repeat: no-repeat; background-position: center;" class="max-w-[50px] max-h-[50px] min-w-[50px] min-h-[50px] rounded-full relative flex items-center justify-center "></div>
        @endif

        <div class="flex flex-col justify-center text-sm w-full">
            <div class="w-full text-[16px]">{{ $title }}</div>

            @if(isset($underTitle))
                <p class="text-gray-300">{{ $underTitle }}</p>
            @endif
        </div>
    </div>

    <section class="h-full flex flex-col px-3">
        {{ $slot }}
    </section>
</div>
