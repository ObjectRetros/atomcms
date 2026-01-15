@props(['classes' => '', 'type' => 'submit'])

<button type="{{ $type }}"
    class="w-full rounded bg-green-600! hover:bg-green-700! text-white border-2 border-green-500 transition! ease-in-out! duration-150! font-semibold px-6! py-2! {{ $classes }}">
    {{ $slot }}
</button>
