@props([
    'itemId' => null,
    'isSelected' => false,
])

@php
    $record = isset($getRecord) ? $getRecord() : null;
    $resolvedItemId = (int) (is_callable($itemId) ? $itemId($record) : $itemId);
    $checked = (bool) (is_callable($isSelected) ? $isSelected($record) : $isSelected);
@endphp

<div
    x-data="{
        id: {{ $resolvedItemId }},
        init() {
            if (!Array.isArray(window.catalogSelIds)) window.catalogSelIds = [];
            if ({{ $checked ? 'true' : 'false' }}) {
                if (!window.catalogSelIds.includes(this.id)) window.catalogSelIds.push(this.id);
            }
            window.dispatchEvent(new CustomEvent('catalog-sel-refresh'));
        },
        toggle(e) {
            if (!Array.isArray(window.catalogSelIds)) window.catalogSelIds = [];
            if (e.target.checked) {
                if (!window.catalogSelIds.includes(this.id)) window.catalogSelIds.push(this.id);
                $wire.toggleSelectItem(this.id, true);
            } else {
                window.catalogSelIds = window.catalogSelIds.filter(x => x !== this.id);
                $wire.toggleSelectItem(this.id, true);
            }
            window.dispatchEvent(new CustomEvent('catalog-sel-refresh'));
        }
    }"
    x-init="init()"
    class="flex items-center justify-center"
>
    <input
        type="checkbox"
        @change="toggle($event)"
        {{ $checked ? 'checked' : '' }}
        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
        aria-label="Select item {{ $resolvedItemId }}"
    />
</div>
