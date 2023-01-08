@props(['badge' => '', 'color' => '#f68b08', 'classes' => ''])

<style>
    .outer-block {
        -moz-column-count: 4;
        -webkit-column-count: 4;
        column-count: 4;
        -moz-column-gap: 1em;
        -webkit-column-gap: 1em;
        column-gap: 1em;
    }

    .items {
        display: inline-block;
        margin: 0 0 1em;
        width: 100%;
    }
</style>


{{-- The commented value below is used for tailwind to generate the abitary value, do not delete. --}}
{{-- bg-[#f68b08] --}}
<div class="w-full flex flex-col grow-0 gap-y-4 rounded overflow-hidden bg-white pb-3 dark:bg-gray-800 shadow self-start float-left {{ $classes }}">
    <div class="flex gap-x-2 border-b p-3 bg-gray-50 dark:bg-gray-900 dark:border-gray-700">
        <div class="bg-[{{ $color }}] max-w-[50px] max-h-[50px] min-w-[50px] min-h-[50px] rounded-full relative flex items-center justify-center">
            <img src="{{ $badge }}.gif" alt="">
        </div>

        <div class="flex flex-col text-sm justify-center">
            <p class="text-black font-semibold dark:text-gray-300">{{ $title }}</p>
            <p class="dark:text-gray-500">{{ $underTitle }}</p>
        </div>
    </div>

    <section class="px-3">
        {{ $slot }}
    </section>
</div>
