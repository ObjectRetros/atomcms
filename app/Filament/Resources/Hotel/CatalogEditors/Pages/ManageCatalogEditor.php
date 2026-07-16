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

class ManageCatalogEditor extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = CatalogEditorResource::class;

    protected string $view = 'filament.resources.hotel.catalog-editors.pages.manage-catalog-editor';

    public ?int $selectedPageId = null;

    public string $searchTerm = '';

    /** Server-driven open state. Once rendered, expand/collapse runs client-side
     *  so drag-and-drop isn't interrupted by Livewire morphs. */
    public array $initialOpenIds = [];

    public function mount(): void
    {
        $this->initialOpenIds = [];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

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

    /** The recursive tree partial reads pagesByParent on every node; without
     *  this the Cache::remember deserialize fires thousands of times per render. */
    private ?Collection $pagesByParentCache = null;

    public function getPagesByParentProperty(): Collection
    {
        return $this->pagesByParentCache ??= $this->tree()->pagesGroupedByParent();
    }

    public function getSelectedPageProperty(): ?CatalogPage
    {
        return $this->selectedPageId ? $this->findCachedPage($this->selectedPageId) : null;
    }

    private function findCachedPage(int $id): ?CatalogPage
    {
        foreach ($this->getPagesByParentProperty() as $children) {
            foreach ($children as $page) {
                if ($page->id === $id) {
                    return $page;
                }
            }
        }

        return null;
    }

    public function getBreadcrumbProperty(): array
    {
        return $this->tree()->breadcrumb($this->getSelectedPageProperty());
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

    public function reorderPages(int $parentId, array $orderedIds): void
    {
        $this->reorderService()->reorderPages($parentId, $orderedIds);
        $this->pagesByParentCache = null;
        Notification::make()->title('Menu order updated')->success()->send();
    }

    public function movePage(int $pageId, int $newParentId, int $insertAtIndex): void
    {
        $this->reorderService()->movePage($pageId, $newParentId, $insertAtIndex);
        $this->pagesByParentCache = null;

        if ($newParentId > 0 && ! in_array($newParentId, $this->initialOpenIds, true)) {
            $this->initialOpenIds[] = $newParentId;
        }

        Notification::make()->title('Page moved')->success()->send();
    }

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

    public function editPageAction(): Action
    {
        return Action::make('editPage')
            ->label('Edit page')
            ->icon('heroicon-m-pencil-square')
            ->visible(fn () => $this->getSelectedPageProperty() !== null)
            ->modalHeading(function (): string {
                $selectedPage = $this->getSelectedPageProperty();

                return $selectedPage ? "Edit: {$selectedPage->caption}" : 'Edit page';
            })
            ->modalWidth('xl')
            ->form(CatalogPageForm::schema())
            ->fillForm(fn () => $this->getSelectedPageProperty()?->only(['caption', 'caption_save', 'order_num', 'icon_image']) ?? [])
            ->action(function (array $data): void {
                $selectedPage = $this->getSelectedPageProperty();

                if (! $selectedPage) {
                    return;
                }

                $selectedPage->update([
                    'caption' => $data['caption'],
                    'caption_save' => CatalogPageForm::sanitizeTag($data['caption_save'] ?? ''),
                    'order_num' => (int) ($data['order_num'] ?? 1),
                    'icon_image' => max(1, (int) ($data['icon_image'] ?: 1)),
                ]);

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
            ->visible(fn () => ($selectedPage = $this->getSelectedPageProperty()) !== null && $selectedPage->parent_id !== -1)
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
            ->visible(fn () => ($selectedPage = $this->getSelectedPageProperty()) !== null && $selectedPage->parent_id !== -1)
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
