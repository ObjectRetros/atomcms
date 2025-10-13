<x-filament-panels::page>
    <script>
        window.catalogSelIds = [];
        window.addEventListener('catalog-sel-update', (e) => {
            window.catalogSelIds = Array.isArray(e.detail?.ids) ? e.detail.ids : [];
        });
    </script>

    <div
        x-data="{ h: 0, set() { this.h = Math.max(320, window.innerHeight - 160) }, init() { this.set(); window.addEventListener('resize', () => this.set()) } }"
        x-init="init()"
        class="w-full"
    >
        <div
            :style="`display:grid !important; grid-template-columns: 320px minmax(0,1fr) !important; gap:1rem !important; height:${h}px !important; overflow:hidden !important; width:100% !important;`"
        >
            <div
                style="
                    height:100% !important;
                    overflow:auto !important;
                    border:1px solid var(--gray-200) !important;
                    border-radius:1rem !important;
                    padding:0.75rem !important;
                    background:var(--filament-color-white, #fff) !important;
                "
                class="dark:bg-gray-900 dark:border-gray-700"
            >
                <h2 class="font-semibold text-lg mb-2">Catalog Pages</h2>
                @php
                    $rootPages = \App\Models\Game\Furniture\CatalogPage::where('parent_id', -1)
                        ->orderBy('order_num')
                        ->get();
                @endphp
                @include('filament.resources.hotel.catalog-editors.pages.partials.catalog-tree', [
                    'pages' => $rootPages,
                    'depth' => 0,
                    'selectedPage' => $selectedPage,
                ])
            </div>

            <div
                style="
                    min-width:0 !important; /* CRITICAL: allows table to shrink */
                    height:100% !important;
                    overflow:hidden !important;
                    border:1px solid var(--gray-200) !important;
                    border-radius:1rem !important;
                    background:var(--filament-color-white, #fff) !important;
                    display:flex !important;
                    flex-direction:column !important;
                "
                class="dark:bg-gray-900 dark:border-gray-700"
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

                        @if($selectedPage && $selectedPage->parent_id !== -1 && ! $this->pageHasLockedItems())
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

                <div style="flex:1 1 auto; min-height:0; overflow:auto; padding:0.75rem;">
                    <div style="min-width:0;">
                        {{ $this->table }}
                        <script>
                            window.catalogSelIds = @json($selectedItemIds ?? []);
                            window.dispatchEvent(new CustomEvent('catalog-sel-refresh'));
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
