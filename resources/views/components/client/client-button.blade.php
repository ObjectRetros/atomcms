@props(['classes' => ''])

<div
    class="text-white rounded bg-[#eeb425] hover:bg-[#e3aa1e] border-[2px] border-[#cf9d15] transition ease-in-out text-center rounded py-1 px-2 text-sm text-white cursor-pointer {{ $classes }}">
    {{ $slot }}
</div>
