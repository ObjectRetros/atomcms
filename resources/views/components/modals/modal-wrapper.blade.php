@props(['classes' => ''])

<div x-data="{ open: false }" class="relative {{ $classes }}">
    {{ $slot }}
</div>
