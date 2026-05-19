@php
    /** @var \Illuminate\Support\Collection $children */
    $children = $this->pagesByParent->get($parentId, collect());
    $iconService = app(\App\Services\Catalog\FurniIconService::class);
@endphp

@if ($children->isNotEmpty())
    <ul
        @class([
            'catalog-tree-list space-y-0.5',
            'ml-3 border-l border-gray-200 pl-2 dark:border-gray-700' => $depth > 0,
        ])
        data-parent-id="{{ $parentId }}"
    >
        @foreach ($children as $page)
            @php
                $hasChildren = $this->pagesByParent->has($page->id);
                $isOpen      = $this->isInitiallyOpen($page->id);
                $isActive    = $this->selectedPageId === $page->id;
            @endphp

            <li
                data-id="{{ $page->id }}"
                data-page-id="{{ $page->id }}"
                @class([
                    'catalog-tree-item',
                    'has-children' => $hasChildren,
                    'is-open' => $hasChildren && $isOpen,
                ])
            >
                <div
                    @class([
                        'group flex items-center gap-1.5 rounded-md px-1.5 py-1 text-sm transition',
                        'bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-300' => $isActive,
                        'hover:bg-gray-100 dark:hover:bg-gray-800' => ! $isActive,
                    ])
                    data-tree-row
                >
                    <span
                        class="catalog-drag-handle cursor-grab text-gray-400 opacity-0 transition group-hover:opacity-100 active:cursor-grabbing"
                        title="Drag to reorder or move to another parent"
                    >
                        <x-filament::icon icon="heroicon-m-bars-3" class="h-3.5 w-3.5" />
                    </span>

                    @if ($hasChildren)
                        <button
                            type="button"
                            class="catalog-tree-toggle grid h-4 w-4 place-items-center text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                            aria-label="Toggle"
                        >
                            <x-filament::icon
                                icon="heroicon-m-chevron-right"
                                class="catalog-tree-chevron h-3 w-3 transition-transform"
                            />
                        </button>
                    @else
                        <span class="block w-4"></span>
                    @endif

                    <img
                        src="{{ $iconService->pageIcon($page->icon_image) }}"
                        alt=""
                        loading="lazy"
                        class="h-4 w-4 shrink-0"
                        style="image-rendering: pixelated"
                    />

                    <button
                        type="button"
                        wire:click="selectPage({{ $page->id }})"
                        class="flex-1 truncate text-left"
                        title="{{ $page->caption }}"
                    >
                        {{ $page->caption }}
                    </button>
                </div>

                {{-- Always render the children if any: client toggles visibility via .is-open --}}
                @if ($hasChildren)
                    @include('filament.resources.hotel.catalog-editors.pages.partials.catalog-tree', [
                        'parentId' => $page->id,
                        'depth'    => $depth + 1,
                    ])
                @endif
            </li>
        @endforeach
    </ul>
@endif
