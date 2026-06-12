@props(['for', 'info' => ''])

<div class="mb-2">
    <label class="block font-semibold text-gray-700 dark:text-gray-200" for="{{ $for }}">
        {{ $slot }}
    </label>

    <p class="text-gray-500 dark:text-gray-400 text-[14px]">
        {{ $info }}
    </p>
</div>
