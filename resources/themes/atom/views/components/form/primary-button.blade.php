@props(['classes' => '', 'type' => 'submit'])

<button type="{{ $type }}"
    class="w-full rounded bg-[#eeb425]! text-white border-2 border-yellow-400 transition! ease-in-out! duration-200! hover:bg-[#d49f1c]! font-semibold px-6! py-2! {{ $classes }}">
    {{ $slot }}
</button>
