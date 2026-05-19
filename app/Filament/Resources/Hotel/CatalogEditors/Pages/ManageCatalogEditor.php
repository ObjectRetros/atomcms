<?php

namespace App\Filament\Resources\Hotel\CatalogEditors\Pages;

use App\Filament\Resources\Hotel\CatalogEditors\CatalogEditorResource;
use App\Filament\Resources\Hotel\CatalogEditors\Forms\CatalogPageForm;
use App\Filament\Resources\Hotel\CatalogEditors\Tables\CatalogItemsTable;
use App\Models\Game\Furniture\CatalogItem;
use App\Models\Game\Furniture\CatalogPage;
use App\Services\Catalog\CatalogReorderService;
use App\Services\Catalog\CatalogTreeService;
use App\Services\Catalog\FurniIconService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

/**
 * Catalog editor page. Holds only the minimal Livewire state needed to drive
 * the UI - the heavy lifting (tree fetch, reordering, search, item moves)
 * lives in dedicated services so each piece is testable in isolation.
 */
class ManageCatalogEditor extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = CatalogEditorResource::class;

    protected string $view = 'filament.resources.hotel.catalog-editors.pages.manage-catalog-editor';

    public ?int $selectedPageId = null;

    public string $searchTerm = '';

    /** Page IDs that should be open on initial render (the path to the
     *  currently selected page, plus any matched search hits). After the
     *  initial paint, expand/collapse is handled entirely client-side -
     *  the server never re-renders the tree just to toggle a node, which
     *  keeps drag-and-drop from being interrupted by Livewire morphs. */
    public array $initialOpenIds = [];

    public function mount(): void
    {
        $this->initialOpenIds = [];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    /* ----- Tree ---------------------------------------------------------- */

    protected function tree(): CatalogTreeService
    {
        return app(CatalogTreeService::class);
    }

    protected function reorderService(): CatalogReorderService
    {
        return app(CatalogReorderService::class);
    }

    protected function icons(): FurniIconService
    {
        return app(FurniIconService::class);
    }

    /**
     * Per-request memoization is critical: the recursive tree partial reads
     * $this->pagesByParent on every node, and Cache::remember deserializes
     * the whole collection on each call. Caching once per request brings the
     * recursive render from thousands of cache fetches to one.
     */
    private ?Collection $pagesByParentCache = null;

    public function getPagesByParentProperty(): Collection
    {
        return $this->pagesByParentCache ??= $this->tree()->pagesGroupedByParent();
    }

    public function getSelectedPageProperty(): ?CatalogPage
    {
        if (! $this->selectedPageId) {
            return null;
        }

        // Read from the cached tree instead of issuing a fresh CatalogPage::find()
        // every render. Selecting a page is the hottest path in this UI.
        foreach ($this->pagesByParent as $children) {
            foreach ($children as $page) {
                if ($page->id === $this->selectedPageId) {
                    return $page;
                }
            }
        }

        return null;
    }

    public function getBreadcrumbProperty(): array
    {
        return $this->tree()->breadcrumb($this->selectedPage);
    }

    public function selectPage(int $pageId): void
    {
        $this->selectedPageId = $pageId;
        $this->initialOpenIds = $this->tree()->expandToReveal(collect([$pageId]));
        $this->resetTable();
    }

    public function isInitiallyOpen(int $pageId): bool
    {
        return in_array($pageId, $this->initialOpenIds, true);
    }

    /* ----- Search -------------------------------------------------------- */

    public function updatedSearchTerm(): void
    {
        $needle = trim($this->searchTerm);

        if ($needle === '') {
            return;
        }

        $page = $this->tree()->searchPages($needle)->first();
        if ($page) {
            $this->selectedPageId = $page->id;
            $this->initialOpenIds = $this->tree()->expandToReveal(collect([$page->id]));
            $this->resetTable();

            return;
        }

        $item = $this->tree()->searchItems($needle)->first();
        if ($item) {
            $this->selectedPageId = $item->page_id;
            $this->initialOpenIds = $this->tree()->expandToReveal(collect([$item->page_id]));
            $this->resetTable();
            Notification::make()->title('Item found, opened its page')->success()->send();
        }
    }

    public function clearSearch(): void
    {
        $this->searchTerm = '';
    }

    /* ----- Page reorder (left tree) ------------------------------------- */

    public function reorderPages(int $parentId, array $orderedIds): void
    {
        $this->reorderService()->reorderPages($parentId, $orderedIds);
        $this->pagesByParentCache = null;
        Notification::make()->title('Menu order updated')->success()->send();
    }

    /**
     * Move a page across parents and to a specific index. Called from the
     * Sortable.js onEnd handler in the tree partial.
     */
    public function movePage(int $pageId, int $newParentId, int $insertAtIndex): void
    {
        $this->reorderService()->movePage($pageId, $newParentId, $insertAtIndex);
        $this->pagesByParentCache = null;

        if ($newParentId > 0 && ! in_array($newParentId, $this->initialOpenIds, true)) {
            $this->initialOpenIds[] = $newParentId;
        }

        Notification::make()->title('Page moved')->success()->send();
    }

    /* ----- Item table --------------------------------------------------- */

    protected function getTableQuery()
    {
        if (! $this->selectedPageId) {
            return CatalogItem::query()->whereRaw('1=0');
        }

        return CatalogItem::query()
            ->where('page_id', $this->selectedPageId)
            ->where('order_number', '!=', -1);
    }

    public function table(Table $table): Table
    {
        return CatalogItemsTable::configure($table, fn () => $this->selectedPageId);
    }

    public function reorderTable(array $order): void
    {
        if (! $this->selectedPageId) {
            return;
        }

        $this->reorderService()->reorderItems($this->selectedPageId, $order);
        Notification::make()->title('Items reordered')->success()->send();
    }

    /* ----- Locked items panel ------------------------------------------- */

    public function getLockedItemsProperty(): Collection
    {
        if (! $this->selectedPageId) {
            return collect();
        }

        return CatalogItem::query()
            ->where('page_id', $this->selectedPageId)
            ->where('order_number', -1)
            ->orderBy('catalog_name')
            ->get();
    }

    public function unlockItem(int $itemId): void
    {
        CatalogItem::whereKey($itemId)
            ->where('page_id', $this->selectedPageId)
            ->update(['order_number' => 99]);

        $this->resetTable();
        Notification::make()->title('Item unlocked')->success()->send();
    }

    /* ----- Page-level header actions ------------------------------------ */

    /**
     * Filament v4 picks public *Action() methods up automatically and renders
     * them via {{ $this->editPageAction }} / etc. in the Blade view.
     */
    public function editPageAction(): Action
    {
        return Action::make('editPage')
            ->label('Edit page')
            ->icon('heroicon-m-pencil-square')
            ->visible(fn () => (bool) $this->selectedPage)
            ->modalHeading(fn () => $this->selectedPage ? "Edit: {$this->selectedPage->caption}" : 'Edit page')
            ->modalWidth('xl')
            ->form(CatalogPageForm::schema())
            ->fillForm(fn () => $this->selectedPage?->only(['caption', 'caption_save', 'order_num', 'icon_image']) ?? [])
            ->action(function (array $data): void {
                if (! $this->selectedPage) {
                    return;
                }

                $this->selectedPage->update([
                    'caption' => $data['caption'],
                    'caption_save' => CatalogPageForm::sanitizeTag($data['caption_save'] ?? ''),
                    'order_num' => (int) ($data['order_num'] ?? 1),
                    'icon_image' => max(1, (int) ($data['icon_image'] ?: 1)),
                ]);

                $this->tree()->flushCache();
                Notification::make()->title('Page updated')->success()->send();
            });
    }

    public function resetSpacingAction(): Action
    {
        return Action::make('resetSpacing')
            ->label('Reset spacing')
            ->icon('heroicon-m-arrow-path')
            ->color('gray')
            ->tooltip('Re-spaces items at 10, 20, 30… while preserving their order. Useful after bulk changes.')
            ->visible(fn () => $this->selectedPage && $this->selectedPage->parent_id !== -1)
            ->action(function (): void {
                $count = $this->reorderService()->resetItemSpacing((int) $this->selectedPageId);

                Notification::make()
                    ->title($count ? "Re-spaced {$count} item(s)" : 'Nothing to re-space')
                    ->success()
                    ->send();
                $this->resetTable();
            });
    }

    public function sortAlphabeticallyAction(): Action
    {
        return Action::make('sortAlphabetically')
            ->label('Sort A→Z')
            ->icon('heroicon-m-bars-arrow-down')
            ->color('gray')
            ->tooltip('Sort items A→Z by name and re-space order_number.')
            ->requiresConfirmation()
            ->modalHeading('Sort items A→Z')
            ->modalDescription('This rewrites order_number for every item on the page. Continue?')
            ->visible(fn () => $this->selectedPage && $this->selectedPage->parent_id !== -1)
            ->action(function (): void {
                $count = $this->reorderService()->sortItemsAlphabetically((int) $this->selectedPageId);

                Notification::make()
                    ->title($count ? "Sorted {$count} item(s)" : 'Nothing to sort')
                    ->success()
                    ->send();
                $this->resetTable();
            });
    }
}
