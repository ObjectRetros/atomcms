
@props(['icon' => '', 'classes' => ''])

<div class="w-full flex flex-col gap-y-4 rounded-lg overflow-hidden bg-[#2b303c] pb-4 shadow-sm text-gray-100 {{ $classes }}">
    <div class="flex gap-x-2 bg-[#21242e] p-3">
        @if (!empty($icon))
            <div class="max-w-12.5 max-h-12.5 min-w-12.5 min-h-12.5 rounded-full relative flex items-center justify-center {{ $icon }}"></div>
        @endif

        <div class="flex flex-col justify-center text-sm">
            <p class="font-semibold text-gray-100">{{ $title }}</p>

            @if(isset($underTitle))
                <p class="text-gray-300">{{ $underTitle }}</p>
            @endif
        </div>
    </div>

    <section class="h-full flex flex-col px-4">
        {{ $slot }}
    </section>
</div>
