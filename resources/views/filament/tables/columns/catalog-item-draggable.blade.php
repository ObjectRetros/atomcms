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
        id: {{ $resolvedItemId }},
        highlight: false,
        dragging: false,

        compute() {
            const arr = Array.isArray(window.catalogSelIds) ? window.catalogSelIds : [];
            this.highlight = arr.includes(this.id);
        },

        dragStart(e) {
            if ({{ $reordering ? 'true' : 'false' }}) return;
            this.dragging = true;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/x-item-id', this.id);
            e.dataTransfer.setDragImage($el, 10, 10);
        },

        dragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            $el.classList.add('ring-2', 'ring-primary-400/60');
        },

        dragLeave(e) {
            $el.classList.remove('ring-2', 'ring-primary-400/60');
        },

        drop(e) {
            e.preventDefault();
            $el.classList.remove('ring-2', 'ring-primary-400/60');
            const srcId = parseInt(e.dataTransfer.getData('text/x-item-id'), 10);
            if (!srcId || srcId === this.id) return;

            const parent = $el.closest('[data-catalog-list]');
            if (!parent) return;

            const children = Array.from(parent.querySelectorAll('[data-item-id]'));
            const ids = children.map(c => parseInt(c.dataset.itemId, 10));
            const srcIndex = ids.indexOf(srcId);
            const destIndex = ids.indexOf(this.id);
            if (srcIndex === -1 || destIndex === -1) return;

            ids.splice(destIndex, 0, ids.splice(srcIndex, 1)[0]);

            window.Livewire.find(parent.dataset.livewireId).call('reorderItems', ids);
        },

        clickRow(e) {
            const multi = !!(e.ctrlKey || e.metaKey);
            $wire.toggleSelectItem(this.id, multi);
        },

        openEditor() {
            $wire.mountTableAction('quickEdit', this.id);
        },
    }"
    x-init="compute(); window.addEventListener('catalog-sel-refresh', compute);"
    draggable="true"
    @dragstart="dragStart"
    @dragover="dragOver"
    @dragleave="dragLeave"
    @drop="drop"
    @click.stop="clickRow"
    @dblclick.stop="openEditor"
    class="flex items-center gap-2 px-2 py-1 rounded cursor-move select-none"
    :class="highlight ? 'bg-blue-50 dark:bg-primary-900/20 ring-1 ring-blue-400/40' : ''"
    :data-item-id="id"
>
    <img src="{{ $resolvedIcon }}" alt="" class="h-6 w-6 shrink-0" loading="lazy" />
    <span class="truncate">{{ $resolvedName }}</span>
</div>
