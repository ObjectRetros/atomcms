@props(['classes' => '', 'type' => 'submit'])

<button type="{{ $type }}"
    class="{{ $classes }} w-full rounded bg-red-500 hover:bg-red-600 text-white p-2 border-2 border-red-400 transition ease-in-out duration-150 font-semibold">
    {{ $slot }}
</button>
