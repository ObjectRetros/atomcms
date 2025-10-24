<x-filament-panels::page class="!max-w-full !px-0">
    <script>
        window.catalogSelIds = [];
        window.addEventListener('catalog-sel-update', (e) => {
            window.catalogSelIds = Array.isArray(e.detail?.ids) ? e.detail.ids : [];
        });
    </script>

    <div
        x-data="{
            h: 0,
            leftWidth: 320,
            resizing: false,
            startX: 0,
            startWidth: 0,
            set() {
                this.h = Math.max(320, window.innerHeight - 160);
            },
            init() {
                this.set();
                window.addEventListener('resize', () => this.set());
                window.addEventListener('mousemove', e => this.doResize(e));
                window.addEventListener('mouseup', () => this.stopResize());
                const saved = localStorage.getItem('catalogEditorLeftWidth');
                if (saved) this.leftWidth = parseInt(saved, 10);

                window.addEventListener('scroll-to-page', e => {
                    const id = e.detail?.id;
                    if (!id) return;
                    const el = document.querySelector(`[data-page-id='${id}']`);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        el.classList.add('ring-2', 'ring-primary-500', 'rounded-md');
                        setTimeout(() => el.classList.remove('ring-2', 'ring-primary-500', 'rounded-md'), 1500);
                    }
                });
            },
            startResize(e) {
                this.resizing = true;
                this.startX = e.clientX;
                this.startWidth = this.leftWidth;
                document.body.style.cursor = 'col-resize';
                $refs.divider.classList.add('bg-primary-400');
            },
            stopResize() {
                if (!this.resizing) return;
                this.resizing = false;
                document.body.style.cursor = '';
                $refs.divider.classList.remove('bg-primary-400');
                localStorage.setItem('catalogEditorLeftWidth', this.leftWidth);
            },
            doResize(e) {
                if (!this.resizing) return;
                const diff = e.clientX - this.startX;
                this.leftWidth = Math.max(200, Math.min(700, this.startWidth + diff));
            },
        }"
        x-init="init()"
        :style="`
            display:grid;
            grid-template-columns:${leftWidth}px 8px 1fr;
            height:${h}px;
            gap:0;
            width:100%;
            overflow:hidden;
        `"
        class="relative select-none"
    >
        <div
            class="dark:bg-gray-900 dark:border-gray-700"
            style="
                height:100%;
                overflow:auto;
                border:1px solid var(--gray-200);
                border-radius:1rem;
                padding:0.75rem;
                background:var(--filament-color-white,#fff);
            "
        >
        
		<div class="mb-3">
    <x-filament::input.wrapper
        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg focus-within:ring-2 focus-within:ring-primary-500 transition"
    >
        <x-filament::input
            wire:model.live.debounce.400ms="pageSearch"
            placeholder="Search catalog pages or items..."
            class="!border-0 !shadow-none !ring-0 !outline-none bg-transparent text-sm"
        />

        <x-slot name="suffix">
            <button
                type="button"
                wire:click="resetView"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-xs font-bold leading-none transition"
                title="Reset to default view"
            >
                X
            </button>
        </x-slot>
    </x-filament::input.wrapper>
</div>

@php
if ($pageSearch !== '') {
    $search = trim($pageSearch);

     $matchedPages = \App\Models\Game\Furniture\CatalogPage::query()
        ->where('caption', 'like', "%{$search}%")
        ->get();

    $matchedItems = \App\Models\Game\Furniture\CatalogItem::query()
        ->where('catalog_name', 'like', "%{$search}%")
        ->orWhere('id', (int) $search)
        ->get(['page_id']);

    $visiblePageIds = collect()
        ->merge($matchedPages->pluck('id'))
        ->merge($matchedItems->pluck('page_id'))
        ->filter()
        ->unique();

    foreach ($visiblePageIds as $pid) {
        $parentId = \App\Models\Game\Furniture\CatalogPage::where('id', $pid)->value('parent_id');
        while ($parentId && $parentId > 0) {
            $visiblePageIds->push($parentId);
            $parentId = \App\Models\Game\Furniture\CatalogPage::where('id', $parentId)->value('parent_id');
        }
    }
    $visiblePageIds = $visiblePageIds->unique();

    $rootPages = \App\Models\Game\Furniture\CatalogPage::query()
        ->where('parent_id', -1)
        ->where(function ($q) use ($visiblePageIds) {
            $q->whereIn('id', $visiblePageIds)
              ->orWhereIn('id', function ($sub) use ($visiblePageIds) {
                  $sub->select('parent_id')
                      ->from('catalog_pages')
                      ->whereIn('id', $visiblePageIds);
              });
        })
        ->orderBy('order_num')
        ->get();

    $expanded = $visiblePageIds->values()->all();
    $this->expandedPages = array_unique(array_merge($this->expandedPages, $expanded));

    if (! $this->selectedPage && $visiblePageIds->isNotEmpty()) {
        $this->selectedPage = \App\Models\Game\Furniture\CatalogPage::find($visiblePageIds->first());
        $this->resetTable();
    }

    $visibleIdsForTree = $visiblePageIds->all();
} else {
    $rootPages = \App\Models\Game\Furniture\CatalogPage::query()
        ->where('parent_id', -1)
        ->orderBy('order_num')
        ->get();

    $visibleIdsForTree = null;
}
@endphp

@include('filament.resources.hotel.catalog-editors.pages.partials.catalog-tree', [
    'pages'        => $rootPages,
    'depth'        => 0,
    'selectedPage' => $selectedPage,
    'visibleIds'   => $visibleIdsForTree,
])


        </div>

        <div
            x-ref="divider"
            x-on:mousedown="startResize"
            class="bg-gray-300 dark:bg-gray-700 hover:bg-primary-400 cursor-col-resize transition-colors duration-150 relative"
            style="
                width:8px;
                height:100%;
                border-left:1px solid rgba(0,0,0,0.05);
                border-right:1px solid rgba(0,0,0,0.05);
            "
        >
            <div class="absolute inset-y-0 left-1/2 -translate-x-1/2 w-px bg-gray-500/40"></div>
        </div>

        <div
            class="dark:bg-gray-900 dark:border-gray-700"
            style="
                min-width:0;
                height:100%;
                overflow:hidden;
                border:1px solid var(--gray-200);
                border-radius:1rem;
                background:var(--filament-color-white,#fff);
                display:flex;
                flex-direction:column;
            "
        >
            <div style="padding:0.75rem; border-bottom:1px solid var(--gray-200);" class="dark:border-gray-700">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-semibold text-lg m-0">
                        @if($selectedPage)
                            Items for: <span class="text-primary-600">{{ $selectedPage->caption }}</span>
                        @else
                            Select a catalog page to view its items
                        @endif
                    </h2>

                    @if($selectedPage && $pageSearch === '' && $selectedPage->parent_id !== -1 && ! $this->pageHasLockedItems())
                        <div class="flex items-center gap-2">
                            <x-filament::button
                                wire:click="autoOrderItems"
                                icon="heroicon-m-arrow-path"
                            >
                                Auto Order Items
                            </x-filament::button>

                            <x-filament::button
                                wire:click="manualOrderItems"
                                icon="heroicon-m-arrow-up-on-square-stack"
                                color="secondary"
                            >
                                Manual Order
                            </x-filament::button>
                        </div>
                    @endif
                </div>

                @if($selectedPage && $selectedPage->parent_id === -1)
                    <p class="mt-2 text-xs text-gray-500">
                        This is a root menu entry. Select a subpage to order its items.
                    </p>
                @elseif($selectedPage && $this->pageHasLockedItems())
                    <p class="mt-2 text-xs text-gray-500">
                        This page contains item(s) with
                        <code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800">order_number = -1</code>.
                        Change or remove them to enable ordering.
                    </p>
                @endif
            </div>

            {{-- Table --}}

<div style="flex:1 1 auto; min-height:0; overflow:auto; padding:0.75rem;">
    <div style="min-width:0;">

        @if($pageSearch !== '')
            <div
                class="mb-2 flex items-center justify-center"
                x-data
                x-transition.opacity.duration.300ms
            >
                <span class="text-[11px] px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-700 shadow-sm">
                    🔍 Search mode active — ordering disabled
                </span>
            </div>
        @endif

        <div
            data-catalog-list
            data-livewire-id="{{ $this->getId() }}"
            class="space-y-0"
            @class([
                'opacity-70 cursor-not-allowed pointer-events-none' => $pageSearch !== '' && ! $selectedPage
            ])
        >
            {{ $this->table }}
        </div>

        <script>
            window.catalogSelIds = @json($selectedItemIds ?? []);
            window.dispatchEvent(new CustomEvent('catalog-sel-refresh'));
        </script>
    </div>
</div>

        </div>
    </div>
</x-filament-panels::page>
