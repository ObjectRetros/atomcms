<x-filament-panels::page>
    {{-- Header card --}}
    <div class="mb-4 flex items-center gap-3 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
        @if ($this->iconUrl)
            <img
                src="{{ $this->iconUrl }}"
                alt=""
                class="h-12 w-12 shrink-0 object-contain"
                style="image-rendering: pixelated"
            />
        @endif
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Catalog item</p>
            <h2 class="truncate text-lg font-semibold text-gray-900 dark:text-white">{{ $record->catalog_name }}</h2>
            <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                catalog_items.id <span class="font-mono">{{ $record->id }}</span>
                @if ($itemBase)
                    &middot; items_base.id <span class="font-mono">{{ $itemBase->id }}</span>
                    &middot; <span class="font-mono">{{ $itemBase->item_name }}</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Tabs --}}
    <div
        class="rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900"
        x-data="{ tab: 'catalog' }"
    >
        <div class="flex border-b border-gray-200 px-3 dark:border-gray-700" role="tablist">
            <button
                type="button"
                @click="tab = 'catalog'"
                :class="tab === 'catalog' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-900 dark:hover:text-gray-200'"
                class="border-b-2 px-3 py-2 text-sm font-medium transition"
            >
                Catalog item
            </button>
            <button
                type="button"
                @click="tab = 'base'"
                :class="tab === 'base' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-900 dark:hover:text-gray-200'"
                class="border-b-2 px-3 py-2 text-sm font-medium transition"
                @if (! $itemBase) disabled @endif
            >
                Items base
            </button>
            <button
                type="button"
                @click="tab = 'rooms'"
                :class="tab === 'rooms' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-900 dark:hover:text-gray-200'"
                class="border-b-2 px-3 py-2 text-sm font-medium transition"
            >
                Placed in rooms
                <span class="ml-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-gray-100 px-1.5 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    {{ $this->roomPlacements->count() }}
                </span>
            </button>
            <button
                type="button"
                @click="tab = 'owners'"
                :class="tab === 'owners' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-900 dark:hover:text-gray-200'"
                class="border-b-2 px-3 py-2 text-sm font-medium transition"
            >
                Owners
                <span class="ml-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-gray-100 px-1.5 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    {{ $this->ownerSummary->count() }}
                </span>
            </button>
        </div>

        {{-- Catalog item tab --}}
        <div x-show="tab === 'catalog'" class="p-5">
            <form wire:submit.prevent="saveCatalog" class="space-y-4">
                {{ $this->catalogForm }}
                <div class="flex justify-end">
                    {{ $this->saveCatalogAction }}
                </div>
            </form>
        </div>

        {{-- Items base tab --}}
        <div x-show="tab === 'base'" x-cloak class="p-5">
            @if ($itemBase)
                <form wire:submit.prevent="saveItemBase" class="space-y-4">
                    {{ $this->itemBaseForm }}
                    <div class="flex justify-end">
                        {{ $this->saveItemBaseAction }}
                    </div>
                </form>
            @else
                <p class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800 dark:border-amber-700/50 dark:bg-amber-900/20 dark:text-amber-200">
                    No matching items_base row for item_ids = <code>{{ $record->item_ids }}</code>.
                    Fix the catalog item's <code>item_ids</code> field on the first tab to point at a valid base id.
                </p>
            @endif
        </div>

        {{-- Rooms tab --}}
        <div x-show="tab === 'rooms'" x-cloak class="p-5">
            @if ($this->roomPlacements->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    No instances of this base item are placed in any room right now.
                </p>
            @else
                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-3 py-2">Room</th>
                                <th class="px-3 py-2">Owner</th>
                                <th class="px-3 py-2">State</th>
                                <th class="px-3 py-2 text-right">Instances</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($this->roomPlacements as $row)
                                <tr>
                                    <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">
                                        {{ $row['room']?->name ?? '#'.$row['room']?->id }}
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($row['room']?->description, 60) }}</p>
                                    </td>
                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-200">
                                        {{ $row['room']?->owner?->username ?? $row['room']?->owner_name ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400">
                                        {{ $row['room']?->state ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-right font-semibold tabular-nums text-gray-900 dark:text-white">
                                        {{ $row['count'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Owners tab --}}
        <div x-show="tab === 'owners'" x-cloak class="p-5">
            @if ($this->ownerSummary->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Nobody owns this base item right now.
                </p>
            @else
                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-3 py-2">User</th>
                                <th class="px-3 py-2">Rank</th>
                                <th class="px-3 py-2 text-right">Owned</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($this->ownerSummary as $row)
                                <tr>
                                    <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">
                                        {{ $row['user']->username }}
                                        <span class="ml-1 text-xs text-gray-400 dark:text-gray-500">#{{ $row['user']->id }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-200">
                                        {{ $row['user']->rank }}
                                    </td>
                                    <td class="px-3 py-2 text-right font-semibold tabular-nums text-gray-900 dark:text-white">
                                        {{ $row['count'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
