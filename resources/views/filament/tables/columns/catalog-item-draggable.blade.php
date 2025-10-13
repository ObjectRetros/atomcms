@props([
    'icon' => '',
    'name' => '',
    'itemId' => null,
    'isSelected' => false,
	'reordering' => false,
])

@php
    $record = isset($getRecord) ? $getRecord() : null;
    $resolvedIcon   = is_callable($icon)   ? $icon($record)   : $icon;
    $resolvedName   = is_callable($name)   ? $name($record)   : $name;
    $resolvedItemId = (int) (is_callable($itemId) ? $itemId($record) : $itemId);
@endphp

<div
    x-data="{
        itemId: {{ $resolvedItemId }},
        highlight: false,
        compute() {
            const arr = Array.isArray(window.catalogSelIds) ? window.catalogSelIds : [];
            this.highlight = arr.includes(this.itemId);
        },
        dragStart(e) {
            @if($reordering)
                e.preventDefault();
                return;
            @endif

            const arr = Array.isArray(window.catalogSelIds) ? window.catalogSelIds : [];
            const ids = arr.includes(this.itemId) ? arr : [this.itemId];
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', ids.join(','));
        },
        clickRow(e) {
            const multi = !!(e.ctrlKey || e.metaKey);
            $wire.toggleSelectItem(this.itemId, multi);
        },
        openEditor() {
            $wire.mountTableAction('quickEdit', this.itemId);
        }
    }"
    x-init="
        compute();
        window.addEventListener('catalog-sel-refresh', () => compute());
    "
    :draggable="{{ $reordering ? 'false' : 'true' }}"
    @dragstart="dragStart($event)"
    @click.stop="clickRow($event)"
    @dblclick.stop="openEditor()"
    class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer"
    :class="highlight ? 'bg-blue-50 dark:bg-primary-900/20 ring-1 ring-blue-400/40' : ''"
    title="Double-click to edit. Drag onto a menu to move. Use the checkbox to multi-select."
>
    <img src="{{ $resolvedIcon }}" alt="" class="h-6 w-6 shrink-0" loading="lazy" />
    <span class="truncate">{{ $resolvedName }}</span>
</div>
