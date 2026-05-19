<x-filament-panels::page class="!max-w-full">
    <style>
        /* Client-side expand/collapse - no Livewire round-trip per click. */
        .catalog-tree-list ul.catalog-tree-list { display: none; }
        .catalog-tree-item.is-open > ul.catalog-tree-list { display: block; }
        .catalog-tree-item.is-open > [data-tree-row] .catalog-tree-chevron { transform: rotate(90deg); }

        /* Drop-target highlight while a drag is over a list */
        .catalog-tree-list.is-drop-target { background: rgb(245 158 11 / 0.06); border-radius: 0.5rem; }
        .catalog-tree-item.is-hover-target > [data-tree-row] {
            outline: 2px dashed rgb(245 158 11 / 0.6);
            outline-offset: -2px;
            border-radius: 0.375rem;
        }
    </style>

    <div
        class="grid grid-cols-1 gap-4 lg:grid-cols-[320px_1fr]"
        x-data="{
            init() {
                this._key = (e) => {
                    if (e.target.matches('input, textarea, [contenteditable]')) return;
                    const search = this.$el.querySelector('input[type=search]');
                    if (e.key === '/') { e.preventDefault(); search?.focus(); }
                    else if (e.key === 'Escape' && search?.value) {
                        search.value = '';
                        search.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                };
                document.addEventListener('keydown', this._key);
            },
            destroy() { document.removeEventListener('keydown', this._key); },
        }"
    >
        {{-- ============= LEFT: TREE ============= --}}
        <aside class="flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
            <x-filament::input.wrapper>
                <x-filament::input
                    type="search"
                    wire:model.live.debounce.300ms="searchTerm"
                    placeholder="Search pages or items..."
                />
                @if ($searchTerm !== '')
                    <x-slot name="suffix">
                        <button
                            type="button"
                            wire:click="clearSearch"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                            title="Clear search"
                        >
                            <x-filament::icon icon="heroicon-m-x-mark" class="h-4 w-4" />
                        </button>
                    </x-slot>
                @endif
            </x-filament::input.wrapper>

            <nav
                class="-mr-2 flex-1 overflow-y-auto pr-2"
                aria-label="Catalog tree"
                x-data="catalogTree"
                x-init="init()"
                wire:ignore.self
            >
                @include('filament.resources.hotel.catalog-editors.pages.partials.catalog-tree', [
                    'parentId' => -1,
                    'depth'    => 0,
                ])
            </nav>

            @once
                @push('scripts')
                <script>
                    // The whole tree is rendered server-side once. Expand / collapse,
                    // drag-and-drop and hover-expand all run on the client so Livewire
                    // never morphs the tree during interactions (which used to kill
                    // the dragged element mid-flight). Only structural changes
                    // (page moved, page selected -> reveal path) round-trip.
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('catalogTree', () => ({
                            sortables: [],
                            hoverTimer: null,
                            hoverLi: null,
                            isDragging: false,

                            init() {
                                this._mountSortables();

                                // Livewire's morph.updated fires once per DOM node it
                                // changes. With a recursive tree of thousands of nodes,
                                // a naive `mount on every fire` ran _mountSortables
                                // thousands of times per cycle and hung the page.
                                // Coalesce to a single trailing call.
                                this._remountScheduled = false;
                                const scheduleRemount = () => {
                                    if (this._remountScheduled) return;
                                    this._remountScheduled = true;
                                    queueMicrotask(() => {
                                        this._remountScheduled = false;
                                        this._mountSortables();
                                    });
                                };
                                Livewire.hook('morph.updated', ({ el }) => {
                                    if (this.$el.contains(el)) scheduleRemount();
                                });

                                // Toggle expand/collapse purely on the client.
                                this.$el.addEventListener('click', (e) => {
                                    const btn = e.target.closest('.catalog-tree-toggle');
                                    if (!btn) return;
                                    e.preventDefault();
                                    e.stopPropagation();
                                    const li = btn.closest('li.catalog-tree-item');
                                    if (li) li.classList.toggle('is-open');
                                });
                            },

                            destroy() {
                                this.sortables.forEach((s) => { try { s.destroy(); } catch (e) {} });
                                this.sortables = [];
                                this._cancelHoverExpand();
                            },

                            // Idempotent: never touches an existing Sortable instance, only
                            // attaches one to <ul>s that don't have one and prunes any whose
                            // element is no longer in the DOM. This is critical during a drag -
                            // tearing down the active Sortable kills the drag mid-flight.
                            _mountSortables() {
                                if (typeof Sortable === 'undefined') return;

                                this.sortables = this.sortables.filter((s) => {
                                    if (s.el?.isConnected) return true;
                                    try { s.destroy(); } catch (e) {}
                                    return false;
                                });

                                this.$el.querySelectorAll('ul.catalog-tree-list').forEach((ul) => {
                                    if (Sortable.get(ul)) return;
                                    this.sortables.push(new Sortable(ul, {
                                        group: 'catalog-pages',
                                        handle: '.catalog-drag-handle',
                                        animation: 150,
                                        ghostClass: 'opacity-40',
                                        chosenClass: 'ring-2',
                                        fallbackOnBody: true,
                                        invertSwap: true,
                                        emptyInsertThreshold: 12,
                                        onStart: () => {
                                            this.isDragging = true;
                                            this.$el.classList.add('is-dragging');
                                        },
                                        onMove: (evt) => this._onMove(evt),
                                        onEnd: (evt) => {
                                            this.isDragging = false;
                                            this.$el.classList.remove('is-dragging');
                                            this._cancelHoverExpand();
                                            this._onDrop(evt);
                                        },
                                    }));
                                });
                            },

                            _onDrop(evt) {
                                const movedId = parseInt(evt.item.dataset.id, 10);
                                const newParent = parseInt(evt.to.dataset.parentId, 10);
                                if (!Number.isFinite(movedId) || !Number.isFinite(newParent)) return;
                                this.$wire.movePage(movedId, newParent, evt.newIndex);
                            },

                            _onMove(evt) {
                                const li = evt.related?.closest?.('li.catalog-tree-item');
                                if (!li) { this._cancelHoverExpand(); return true; }

                                if (li === this.hoverLi) return true;

                                this._cancelHoverExpand();
                                this.hoverLi = li;
                                li.classList.add('is-hover-target');

                                if (li.classList.contains('has-children') && ! li.classList.contains('is-open')) {
                                    this.hoverTimer = setTimeout(() => {
                                        // Pure client-side toggle - no Livewire morph,
                                        // so the dragged item keeps its DOM identity.
                                        li.classList.add('is-open');
                                        // Idempotent mount: attach Sortable to the freshly
                                        // visible nested <ul> so it joins the 'catalog-pages'
                                        // group and accepts drops, without touching the
                                        // active in-flight Sortable instance.
                                        this._mountSortables();
                                    }, 450);
                                }

                                return true;
                            },

                            _cancelHoverExpand() {
                                if (this.hoverTimer) clearTimeout(this.hoverTimer);
                                this.hoverTimer = null;
                                if (this.hoverLi) this.hoverLi.classList.remove('is-hover-target');
                                this.hoverLi = null;
                            },
                        }));
                    });
                </script>
                @endpush
            @endonce
        </aside>

        {{-- ============= RIGHT: ITEMS ============= --}}
        <section class="flex min-w-0 flex-col rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
            @if ($this->selectedPage)
                <header class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <nav aria-label="Breadcrumb" class="mb-1 flex flex-wrap items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                        @foreach ($this->breadcrumb as $i => $crumb)
                            @if ($i > 0)
                                <x-filament::icon icon="heroicon-m-chevron-right" class="h-3 w-3" />
                            @endif
                            <button
                                type="button"
                                wire:click="selectPage({{ $crumb->id }})"
                                class="hover:text-primary-600 dark:hover:text-primary-400 transition"
                            >
                                {{ $crumb->caption }}
                            </button>
                        @endforeach
                    </nav>
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ $this->selectedPage->caption }}
                        </h2>
                        <div class="flex items-center gap-2">
                            {{ $this->editPageAction }}
                            {{ $this->resetSpacingAction }}
                            {{ $this->sortAlphabeticallyAction }}
                        </div>
                    </div>
                </header>

                @if ($this->lockedItems->isNotEmpty())
                    <div class="border-b border-amber-200 bg-amber-50 px-4 py-3 text-xs dark:border-amber-700/50 dark:bg-amber-900/20">
                        <p class="font-semibold text-amber-800 dark:text-amber-200">
                            {{ $this->lockedItems->count() }} locked item(s) on this page
                        </p>
                        <p class="mt-0.5 text-amber-700 dark:text-amber-300/80">
                            Items with <code class="rounded bg-amber-100 px-1 dark:bg-amber-900">order_number = -1</code>
                            are excluded from drag/sort. Unlock them to include in ordering.
                        </p>
                        <ul class="mt-2 flex flex-wrap gap-1.5">
                            @foreach ($this->lockedItems as $locked)
                                <li class="inline-flex items-center gap-1 rounded border border-amber-300 bg-white px-2 py-0.5 text-[11px] dark:border-amber-700 dark:bg-amber-950">
                                    <span class="font-mono text-amber-900 dark:text-amber-100">{{ $locked->catalog_name }}</span>
                                    <button
                                        type="button"
                                        wire:click="unlockItem({{ $locked->id }})"
                                        class="text-amber-600 hover:text-amber-900 dark:text-amber-300 dark:hover:text-amber-100"
                                        title="Unlock"
                                    >
                                        <x-filament::icon icon="heroicon-m-lock-open" class="h-3 w-3" />
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex-1 overflow-auto">
                    {{ $this->table }}
                </div>
            @else
                <div class="flex flex-1 flex-col items-center justify-center gap-3 p-10 text-center">
                    <x-filament::icon icon="heroicon-o-rectangle-stack" class="h-12 w-12 text-gray-300 dark:text-gray-600" />
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Pick a page to begin</h2>
                    <p class="max-w-sm text-sm text-gray-500 dark:text-gray-400">
                        Select a catalog page from the tree on the left to view, sort and edit its items.
                        Drag pages to re-order; drag items to re-sort within a page.
                    </p>
                </div>
            @endif
        </section>
    </div>
</x-filament-panels::page>
