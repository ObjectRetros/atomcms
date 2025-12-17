<ul class="pl-{{ $depth * 4 }} text-sm">
    @foreach ($pages as $index => $page)
        @if ($depth === 0 && $index > 0)
            {{-- visible dotted horizontal separator between main menu groups --}}
            <li class="list-none my-2">
                <div
                    style="
                        width: 100%;
                        height: 1px;
                        background-image: radial-gradient(currentColor 1px, transparent 1.5px);
                        background-size: 6px 1px;
                        color: rgba(156,163,175,0.6); /* gray-400 with opacity */
                        display: block;
                    "
                    class="dark:color-[rgba(107,114,128,0.7)]"
                ></div>
            </li>
        @endif

        @php
            $filterIds = $visibleIds ?? null;
            $children = \App\Models\Game\Furniture\CatalogPage::query()
                ->where('parent_id', $page->id)
                ->when($filterIds !== null, fn ($q) => $q->whereIn('id', $filterIds))
                ->orderBy('order_num')
                ->orderBy('id')
                ->get();

            $shouldShow = $filterIds === null
                ? true
                : in_array($page->id, $filterIds, true) || $children->isNotEmpty();

            if (! $shouldShow) {
                continue;
            }

            $hasChildren = $children->isNotEmpty();
            $iconUrl     = $this->buildCatalogIconUrl((int) $page->icon_image);
            $fallbackUrl = $this->buildCatalogIconUrl(1);
        @endphp

        <li
            data-page-id="{{ $page->id }}"
            class="group flex items-center gap-1 min-w-0 rounded transition-all duration-150"
            @dragover.prevent.stop="
                if (!event.dataTransfer.types.includes('text/x-page-id')) return;
                const rect = $el.getBoundingClientRect();
                const mid  = rect.top + rect.height / 2;
                $el.dataset.dropPos = (event.clientY < mid) ? 'before' : 'after';
                $el.classList.add('ring-2','ring-primary-400/60');
            "
            @dragleave.stop="
                $el.classList.remove('ring-2','ring-primary-400/60');
                delete $el.dataset.dropPos;
            "
            @drop.prevent.stop="
                const src = event.dataTransfer.getData('text/x-page-id');
                if (src && src !== '{{ $page->id }}') {
                    const pos = $el.dataset.dropPos || 'after';
                    $wire.reorderPage(parseInt(src, 10), {{ $page->id }}, pos);
                }
                $el.classList.remove('ring-2','ring-primary-400/60');
                delete $el.dataset.dropPos;
            "
        >
            @if ($hasChildren)
                <x-filament::icon-button
                    :icon="$this->isExpanded($page->id) ? 'heroicon-s-chevron-down' : 'heroicon-s-chevron-right'"
                    wire:click="toggleExpand({{ $page->id }})"
                    label="{{ $this->isExpanded($page->id) ? 'Collapse' : 'Expand' }}"
                    tooltip="{{ $this->isExpanded($page->id) ? 'Collapse' : 'Expand' }}"
                    size="xs"
                    color="gray"
                    variant="ghost"
                    class="shrink-0 inline-flex"
                    style="display:inline-flex;vertical-align:middle;"
                />
            @else
                <span class="inline-flex h-5 w-5 shrink-0"></span>
            @endif

            <span
                x-data
                draggable="true"
                @dragstart="
                    event.dataTransfer.setData('text/x-page-id', '{{ $page->id }}');
                    event.dataTransfer.effectAllowed = 'move';
                "
                class="inline-flex h-5 w-5 shrink-0 items-center justify-center cursor-move
                       text-gray-400 dark:text-gray-500
                       opacity-0 group-hover:opacity-100 transition-opacity"
                title="Drag to reorder within this level"
                style="display:inline-flex;vertical-align:middle;"
            >
                <svg width="12" height="12" viewBox="0 0 12 12" aria-hidden="true">
                    <circle cx="3" cy="3" r="1.2" fill="currentColor"></circle>
                    <circle cx="9" cy="3" r="1.2" fill="currentColor"></circle>
                    <circle cx="3" cy="6" r="1.2" fill="currentColor"></circle>
                    <circle cx="9" cy="6" r="1.2" fill="currentColor"></circle>
                    <circle cx="3" cy="9" r="1.2" fill="currentColor"></circle>
                    <circle cx="9" cy="9" r="1.2" fill="currentColor"></circle>
                </svg>
            </span>

            <button
                x-data="{
                    over: false,
                    clickTimer: null,
                    clickDelay: 350,
                    singleClick() {
                        clearTimeout(this.clickTimer);
                        this.clickTimer = setTimeout(() => { $wire.selectPage({{ $page->id }}); }, this.clickDelay);
                    },
                    doubleClick() {
                        clearTimeout(this.clickTimer);
                        $wire.openEditPage({{ $page->id }});
                    },
                }"
                @dragover.prevent.stop="
                    if (event.dataTransfer.types.includes('text/x-page-id')) return;
                    over = true
                "
                @dragleave.prevent.stop="over = false"
                @drop.prevent.stop="
                    if (event.dataTransfer.types.includes('text/x-page-id')) return;
                    over = false;
                    const payload = event.dataTransfer.getData('text/plain');
                    if (payload) { $wire.moveItemsToPage(payload, {{ $page->id }}) }
                "
                @click.stop.prevent="singleClick()"
                @dblclick.stop.prevent="doubleClick()"
                class="flex-1 min-w-0 inline-flex items-center gap-0.5 px-2 py-1 rounded
                       hover:bg-gray-100 dark:hover:bg-gray-800 whitespace-nowrap
                       transition-all duration-150
                       {{ $selectedPage && $selectedPage->id === $page->id ? 'bg-gray-200 dark:bg-gray-700 font-semibold' : '' }}"
                :class="over ? 'ring-2 ring-primary-500/50 bg-primary-50 dark:bg-primary-900/10' : ''"
                title="Click to select. Double-click to edit. Drop items here to move."
                style="display:inline-flex;vertical-align:middle;"
            >
                <span class="inline-block h-5 w-5 shrink-0"></span>

                <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center">
                    <img
                        src="{{ $iconUrl }}"
                        alt=""
                        class="max-w-full max-h-full object-contain align-middle"
                        loading="lazy"
                        referrerpolicy="no-referrer"
                        onerror="this.onerror=null;this.src='{{ $fallbackUrl }}';"
                        style="image-rendering: pixelated; image-rendering: crisp-edges;"
                    />
                </span>

                <span class="truncate inline-block" style="display:inline-block;vertical-align:middle;">
                    {{ $page->caption }}
                </span>
            </button>

            @if ($hasChildren && $this->isExpanded($page->id))
                @include('filament.resources.hotel.catalog-editors.pages.partials.catalog-tree', [
                    'pages'        => $children,
                    'depth'        => $depth + 1,
                    'selectedPage' => $selectedPage,
                    'visibleIds'   => $filterIds,
                ])
            @endif
        </li>
    @endforeach
</ul>
