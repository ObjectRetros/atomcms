<?php

namespace App\Filament\Resources\Hotel\CatalogEditors\Pages;

use App\Filament\Resources\Hotel\CatalogEditors\CatalogEditorResource;
use App\Models\Game\Furniture\CatalogItem;
use App\Models\Game\Furniture\CatalogPage;
use App\Models\Miscellaneous\WebsiteSetting;
use Filament\Actions\Action as FilamentAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class ManageCatalogEditor extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = CatalogEditorResource::class;

    protected string $view = 'filament.resources.hotel.catalog-editors.pages.manage-catalog-editor';

    public ?CatalogPage $selectedPage = null;

    public array $expandedPages = [];

    public array $selectedItemIds = [];

    public function selectPage(int $pageId): void
    {
        $this->selectedPage = CatalogPage::find($pageId);
        $this->selectedItemIds = [];
        $this->resetTable();
    }

    public function toggleExpand(int $pageId): void
    {
        if (in_array($pageId, $this->expandedPages, true)) {
            $this->expandedPages = array_values(array_diff($this->expandedPages, [$pageId]));
        } else {
            $this->expandedPages[] = $pageId;
        }
    }

    public function isExpanded(int $pageId): bool
    {
        return in_array($pageId, $this->expandedPages, true);
    }

    public function toggleSelectItem(int $itemId, bool $ctrl = false): void
    {
        if ($ctrl) {
            if (in_array($itemId, $this->selectedItemIds, true)) {
                $this->selectedItemIds = array_values(array_diff($this->selectedItemIds, [$itemId]));
            } else {
                $this->selectedItemIds[] = $itemId;
            }
        } else {
            $this->selectedItemIds = [$itemId];
        }

        $this->resetTable();
    }

    public function getTableQuery()
    {
        return $this->selectedPage
            ? CatalogItem::query()
                ->where('page_id', $this->selectedPage->id)
                ->orderBy('order_number')
                ->orderBy('catalog_name')
                ->orderBy('id') // stable
            : CatalogItem::query()->whereRaw('1=0');
    }

    protected function findPrevNeighbor(CatalogItem $record): ?CatalogItem
    {
        return CatalogItem::query()
            ->where('page_id', $record->page_id)
            ->where('order_number', '!=', -1)
            ->where(function ($q) use ($record) {
                $q->where('order_number', '<', $record->order_number)
                    ->orWhere(function ($q2) use ($record) {
                        $q2->where('order_number', $record->order_number)
                            ->where('id', '<', $record->id);
                    });
            })
            ->orderBy('order_number', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }

    protected function findNextNeighbor(CatalogItem $record): ?CatalogItem
    {
        return CatalogItem::query()
            ->where('page_id', $record->page_id)
            ->where('order_number', '!=', -1)
            ->where(function ($q) use ($record) {
                $q->where('order_number', '>', $record->order_number)
                    ->orWhere(function ($q2) use ($record) {
                        $q2->where('order_number', $record->order_number)
                            ->where('id', '>', $record->id);
                    });
            })
            ->orderBy('order_number', 'asc')
            ->orderBy('id', 'asc')
            ->first();
    }

    protected function canMoveUp(CatalogItem $record): bool
    {
        if ($record->order_number === -1) {
            return false;
        }

        return (bool) $this->findPrevNeighbor($record);
    }

    protected function canMoveDown(CatalogItem $record): bool
    {
        if ($record->order_number === -1) {
            return false;
        }

        return (bool) $this->findNextNeighbor($record);
    }

    protected function nudgeRecord(CatalogItem $record, string $direction): void
    {
        if ($record->order_number === -1) {
            Notification::make()->title('Locked')->body('This item is locked (order = -1).')->danger()->send();

            return;
        }

        $neighbor = $direction === 'up'
            ? $this->findPrevNeighbor($record)
            : $this->findNextNeighbor($record);

        if (! $neighbor) {
            return;
        }

        DB::transaction(function () use ($record, $neighbor) {
            $a = $record->order_number;
            $b = $neighbor->order_number;

            $record->update(['order_number' => $b]);
            $neighbor->update(['order_number' => $a]);
        });

        $this->normalizeOrderForSelectedPage();

        Notification::make()->title('Order updated')->success()->send();
    }

    protected function normalizeOrderForSelectedPage(): void
    {
        if (! $this->selectedPage?->id) {
            return;
        }

        $items = CatalogItem::query()
            ->where('page_id', $this->selectedPage->id)
            ->where('order_number', '!=', -1)
            ->orderBy('order_number')
            ->orderBy('id')
            ->get(['id']);

        DB::transaction(function () use ($items) {
            foreach ($items->values() as $index => $item) {
                CatalogItem::whereKey($item->id)
                    ->update(['order_number' => ($index + 1) * 10]);
            }
        });

        $this->resetTable();
    }

    public function pageHasLockedItems(): bool
    {
        if (! $this->selectedPage?->id) {
            return false;
        }

        return CatalogItem::query()
            ->where('page_id', $this->selectedPage->id)
            ->where('order_number', -1)
            ->exists();
    }

    public function autoOrderItems(): void
    {
        if (! $this->selectedPage?->id) {
            Notification::make()->title('Select a page first')->warning()->send();

            return;
        }

        if ($this->pageHasLockedItems()) {
            Notification::make()
                ->title('Action not allowed')
                ->body('This page contains item(s) with order_number = -1. Remove or change them before auto-ordering.')
                ->danger()
                ->send();

            return;
        }

        $affected = CatalogItem::query()
            ->where('page_id', $this->selectedPage->id)
            ->where('order_number', '!=', -1)
            ->update(['order_number' => 99]);

        $this->resetTable();

        if ($affected > 0) {
            Notification::make()->title('Items auto-ordered')->body("Updated {$affected} item(s).")->success()->send();
        } else {
            Notification::make()->title('Nothing to update')->body('No items were changed (none on this page or all are set to -1).')->warning()->send();
        }
    }

    public function manualOrderItems(): void
    {
        if (! $this->selectedPage?->id) {
            Notification::make()->title('Select a page first')->warning()->send();

            return;
        }

        if ($this->pageHasLockedItems()) {
            Notification::make()->title('Action not allowed')->body('This page contains item(s) with order_number = -1. Change/remove them before manual ordering.')->danger()->send();

            return;
        }

        $items = CatalogItem::query()
            ->where('page_id', $this->selectedPage->id)
            ->where('order_number', '!=', -1)
            ->orderBy('catalog_name', 'asc')
            ->orderBy('id', 'asc')
            ->get(['id']);

        if ($items->isEmpty()) {
            Notification::make()->title('Nothing to update')->body('No items on this page (or all are locked to -1).')->warning()->send();

            return;
        }

        DB::transaction(function () use ($items) {
            foreach ($items->values() as $index => $item) {
                CatalogItem::whereKey($item->id)->update(['order_number' => ($index + 1) * 10]);
            }
        });

        $this->resetTable();

        Notification::make()->title('Items manually ordered')->body('Items sorted A→Z and numbered 10, 20, 30, …')->success()->send();
    }

    public function table(Table $table): Table
    {
        return $table->paginated(false);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\ViewColumn::make('select_item')
                ->label('')
                ->view('filament.tables.columns.catalog-item-select')
                ->viewData([
                    'itemId' => fn ($record) => $record->id,
                    'isSelected' => fn ($record) => in_array($record->id, $this->selectedItemIds, true),
                ])
                ->width('36px')
                ->sortable(false)
                ->searchable(false),

            Tables\Columns\ViewColumn::make('item_display')
                ->label('Item')
                ->view('filament.tables.columns.catalog-item-draggable')
                ->viewData([
                    'icon' => fn ($record) => $this->buildFurniIconUrl($record->catalog_name),
                    'name' => fn ($record) => $record->catalog_name,
                    'itemId' => fn ($record) => $record->id,
                    'isSelected' => fn ($record) => in_array($record->id, $this->selectedItemIds, true),
                ])
                ->sortable(false)
                ->searchable(false),

            Tables\Columns\TextColumn::make('cost_credits')->label('Credits'),
            Tables\Columns\TextColumn::make('cost_points')->label('Points'),
            Tables\Columns\TextColumn::make('points_type')->label('Type'),
            Tables\Columns\TextColumn::make('amount')->label('Amount'),

            Tables\Columns\TextColumn::make('order_number')
                ->label('Order')
                ->sortable()
                ->toggleable(),

            Tables\Columns\IconColumn::make('club_only')->boolean()->label('Club Only'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            FilamentAction::make('move_up')
                ->label('')
                ->icon('heroicon-m-chevron-up')
                ->color('gray')
                ->tooltip('Move up')
                ->action(fn (\App\Models\Game\Furniture\CatalogItem $record) => $this->nudgeRecord($record, 'up'))
                ->visible(fn (\App\Models\Game\Furniture\CatalogItem $record) => $this->canMoveUp($record))
                ->size('sm'),

            FilamentAction::make('move_down')
                ->label('')
                ->icon('heroicon-m-chevron-down')
                ->color('gray')
                ->tooltip('Move down')
                ->action(fn (\App\Models\Game\Furniture\CatalogItem $record) => $this->nudgeRecord($record, 'down'))
                ->visible(fn (\App\Models\Game\Furniture\CatalogItem $record) => $this->canMoveDown($record))
                ->size('sm'),

            EditAction::make('edit')
                ->label('Edit')
                ->icon('heroicon-m-pencil-square')
                ->modalHeading('Edit catalog item')
                ->modalSubmitActionLabel('Save')
                ->modalWidth('md')
                ->form([
                    Forms\Components\TextInput::make('cost_credits')->label('Credits')->numeric()->minValue(0)->required(),
                    Forms\Components\TextInput::make('cost_points')->label('Points')->numeric()->minValue(0)->required(),
                    Forms\Components\TextInput::make('points_type')->label('Type')->numeric()->minValue(0)->maxValue(999)->maxLength(50),
                    Forms\Components\TextInput::make('amount')->label('Amount')->numeric()->minValue(1)->default(1)->required(),
                    Forms\Components\TextInput::make('order_number')
                        ->label('Order')
                        ->numeric()
                        ->minValue(-1)
                        ->step(1)
                        ->helperText('Use -1 to lock, or a non-negative number to sort (lower = earlier).')
                        ->required(),
                    Forms\Components\Toggle::make('club_only')->label('Club only'),
                ])
                ->fillForm(fn (\App\Models\Game\Furniture\CatalogItem $record) => [
                    'cost_credits' => $record->cost_credits,
                    'cost_points' => $record->cost_points,
                    'points_type' => $record->points_type,
                    'amount' => $record->amount,
                    'order_number' => $record->order_number,
                    'club_only' => $record->club_only === '1',
                ])
                ->action(function (\App\Models\Game\Furniture\CatalogItem $record, array $data): void {
                    $record->update([
                        'cost_credits' => (int) $data['cost_credits'],
                        'cost_points' => (int) $data['cost_points'],
                        'points_type' => $data['points_type'] ?? null,
                        'amount' => (int) $data['amount'],
                        'order_number' => (int) $data['order_number'],
                        'club_only' => ! empty($data['club_only']) ? '1' : '0',
                    ]);

                    $this->resetTable();

                    Notification::make()->title('Item updated')->success()->send();
                }),
        ];
    }

    protected function getActions(): array
    {
        return [
            FilamentAction::make('editPage')
                ->label('Edit page')
                ->modalHeading(function (array $arguments): string {
                    $page = CatalogPage::find($arguments['pageId'] ?? null);

                    return $page ? 'Edit: ' . $page->caption : 'Edit page';
                })
                ->modalSubmitActionLabel('Save')
                ->modalWidth('3xl')
                ->form([
                    Forms\Components\TextInput::make('caption')->label('Name')->maxLength(128)->required(),

                    Forms\Components\TextInput::make('caption_save')
                        ->label('Name TAG')
                        ->maxLength(25)
                        ->nullable()
                        ->extraInputAttributes([
                            'pattern' => '[a-z]*',
                            'title' => 'Lowercase letters only (a–z); leave empty if you want.',
                            'spellcheck' => 'false',
                            'autocomplete' => 'off',
                        ])
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state === null || $state === '') {
                                $set('caption_save', '');

                                return;
                            }
                            $set('caption_save', strtolower(preg_replace('/[^a-z]/', '', $state)));
                        })
                        ->rules(['nullable', 'regex:/^[a-z]+$/'])
                        ->validationMessages([
                            'regex' => 'Use lowercase letters only (a–z), no spaces or special characters.',
                        ]),

                    Forms\Components\TextInput::make('order_num')
                        ->label('Order')
                        ->numeric()
                        ->minValue(0)
                        ->step(1)
                        ->required()
                        ->helperText('Lower number appears earlier in the menu.'),

                    Forms\Components\TextInput::make('icon_image')
                        ->label('Icon number')
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->default(1)
                        ->live()
                        ->helperText(function ($get) {
                            $id = (int) ($get('icon_image') ?: 1);
                            $url = $this->buildCatalogIconUrl($id);
                            $fallback = $this->buildCatalogIconUrl(1);

                            $html = '<div class="mt-2 flex items-center gap-3">
                                <img src="' . e($url) . '" alt="icon ' . e($id) . '" class="h-8 w-8 object-contain"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src=\'' . e($fallback) . '\'"
                                    style="image-rendering: pixelated; image-rendering: crisp-edges;">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Icon #' . e($id) . '</span>
                            </div>';

                            return new \Illuminate\Support\HtmlString($html);
                        }),
                ])
                ->fillForm(function (array $arguments): array {
                    $page = CatalogPage::find($arguments['pageId'] ?? null);

                    return [
                        'caption' => $page?->caption ?? '',
                        'caption_save' => $page?->caption_save ?? '',
                        'order_num' => $page?->order_num ?? 1,
                        'icon_image' => $page?->icon_image ?? 1,
                    ];
                })
                ->action(function (array $data, array $arguments): void {
                    $page = CatalogPage::find($arguments['pageId'] ?? null);
                    if (! $page) {
                        Notification::make()->title('Page not found')->danger()->send();

                        return;
                    }

                    $tag = $data['caption_save'] ?? '';
                    if ($tag !== '') {
                        $tag = strtolower(preg_replace('/[^a-z]/', '', $tag));
                    }

                    $icon = max(1, (int) ($data['icon_image'] ?: 1));

                    $page->update([
                        'caption' => $data['caption'],
                        'caption_save' => $tag,
                        'order_num' => (int) ($data['order_num'] ?? 1),
                        'icon_image' => $icon,
                    ]);

                    $this->selectPage($page->id);
                    Notification::make()->title('Page updated')->success()->send();
                }),
        ];
    }

    public function reorderPage(int $pageId, int $targetPageId, string $position = 'after'): void
    {
        $page = CatalogPage::find($pageId);
        $target = CatalogPage::find($targetPageId);

        if (! $page || ! $target) {
            return;
        }

        if ((int) $page->parent_id !== (int) $target->parent_id) {
            return;
        }

        // nothing to do
        if ($page->id === $target->id) {
            return;
        }

        $siblings = CatalogPage::query()
            ->where('parent_id', $page->parent_id)
            ->orderBy('order_num')
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        $siblings = array_values(array_filter($siblings, fn ($id) => (int) $id !== (int) $page->id));

        $targetIndex = array_search($target->id, $siblings, true);
        if ($targetIndex === false) {
            return;
        }

        if ($position === 'before') {
            array_splice($siblings, $targetIndex, 0, [$page->id]);
        } else {
            array_splice($siblings, $targetIndex + 1, 0, [$page->id]);
        }

        DB::transaction(function () use ($siblings) {
            foreach ($siblings as $i => $id) {
                CatalogPage::whereKey($id)->update(['order_num' => ($i + 1) * 10]);
            }
        });

        if ($this->selectedPage?->id) {
            $this->selectedPage = CatalogPage::find($this->selectedPage->id);
        }

        Notification::make()->title('Menu order updated')->success()->send();
    }

    public function normalizePageOrder(int $parentId): void
    {
        $ids = CatalogPage::query()
            ->where('parent_id', $parentId)
            ->orderBy('order_num')
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        DB::transaction(function () use ($ids) {
            foreach ($ids as $i => $id) {
                CatalogPage::whereKey($id)->update(['order_num' => ($i + 1) * 10]);
            }
        });
    }

    public function openEditPage(int $pageId): void
    {
        $this->mountAction('editPage', ['pageId' => $pageId]);
    }

    public function moveItemToPage(int $itemId, int $targetPageId): void
    {
        $this->moveItemsToPage((string) $itemId, $targetPageId);
    }

    public function moveItemsToPage(string $itemIdsCsv, int $targetPageId): void
    {
        $ids = collect(explode(',', $itemIdsCsv))
            ->map(fn ($v) => (int) trim($v))
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values()
            ->all();

        $target = CatalogPage::find($targetPageId);

        if (empty($ids) || ! $target) {
            Notification::make()->title('Move failed')->body('No items selected or target page not found.')->danger()->send();

            return;
        }

        DB::transaction(function () use ($ids, $targetPageId) {
            $maxOrder = (int) (CatalogItem::where('page_id', $targetPageId)->max('order_number') ?? 0);

            foreach ($ids as $i => $id) {
                CatalogItem::whereKey($id)->update([
                    'page_id' => $targetPageId,
                    'order_number' => $maxOrder + 1 + $i,
                ]);
            }
        });

        $this->resetTable();
        $this->selectedItemIds = [];

        Notification::make()
            ->title('Items moved')
            ->body('Moved ' . count($ids) . ' item(s) to: ' . ($target->caption ?? ('#' . $targetPageId)))
            ->success()
            ->send();
    }

    protected function buildFurniIconUrl(string $catalogName): string
    {
        $base = $this->getFurniIconBasePath();
        $safeName = str_replace('*', '_', $catalogName);
        $path = rtrim($base, '/') . '/' . $safeName . '_icon.png';

        if (preg_match('#^(https?:)?//#', $path)) {
            return $path;
        }

        return asset($path);
    }

    protected function getFurniIconBasePath(): string
    {
        $setting = WebsiteSetting::where('key', 'furniture_icons_path')->first();

        return $setting && $setting->value ? rtrim($setting->value, '/') : '/images/furniture';
    }

    protected function getCatalogIconBasePath(): string
    {
        $setting = WebsiteSetting::where('key', 'catalog_icons_path')->first();

        return $setting && $setting->value ? rtrim($setting->value, '/') : '/gamedata/c_images/catalogue';
    }

    protected function buildCatalogIconUrl(int $iconImage): string
    {
        $iconImage = $iconImage > 0 ? $iconImage : 1;
        $base = $this->getCatalogIconBasePath();
        $path = $base . '/icon_' . $iconImage . '.png';

        if (preg_match('#^(https?:)?//#', $path)) {
            return $path;
        }

        return asset($path);
    }

    protected function getTableHeaderActions(): array
    {
        return [
            FilamentAction::make('massEdit')
                ->label('Mass edit selected')
                ->icon('heroicon-m-pencil-square')
                ->color('primary')
                ->disabled(fn () => empty($this->selectedItemIds))
                ->modalHeading('Edit selected catalog items')
                ->modalSubmitActionLabel('Apply changes')
                ->modalWidth('lg')
                ->form([
                    Forms\Components\TextInput::make('cost_credits')->label('Credits')->numeric()->minValue(0)->nullable()->helperText('Leave empty to keep unchanged'),
                    Forms\Components\TextInput::make('cost_points')->label('Points')->numeric()->minValue(0)->nullable()->helperText('Leave empty to keep unchanged'),
                    Forms\Components\TextInput::make('points_type')->label('Type (points_type)')->numeric()->minValue(0)->maxValue(999)->nullable()->helperText('Leave empty to keep unchanged'),
                    Forms\Components\TextInput::make('amount')->label('Amount')->numeric()->minValue(1)->nullable()->helperText('Leave empty to keep unchanged'),
                    Forms\Components\TextInput::make('order_number')->label('Order')->numeric()->minValue(-1)->nullable()->helperText('Leave empty to keep unchanged'),
                    Forms\Components\Select::make('club_only')
                        ->label('Club only')
                        ->options(['' => '— No change —', '1' => 'Yes', '0' => 'No'])
                        ->native(false)
                        ->nullable()
                        ->default('')
                        ->helperText('Choose Yes/No, or leave as "No change"'),
                ])
                ->action(function (array $data): void {
                    $ids = collect($this->selectedItemIds)
                        ->filter(fn ($v) => (int) $v > 0)
                        ->map(fn ($v) => (int) $v)
                        ->values()
                        ->all();

                    if (empty($ids)) {
                        Notification::make()->title('No items selected')->warning()->send();

                        return;
                    }

                    $updates = [];

                    if ($data['cost_credits'] !== null && $data['cost_credits'] !== '') {
                        $updates['cost_credits'] = (int) $data['cost_credits'];
                    }
                    if ($data['cost_points'] !== null && $data['cost_points'] !== '') {
                        $updates['cost_points'] = (int) $data['cost_points'];
                    }
                    if ($data['points_type'] !== null && $data['points_type'] !== '') {
                        $updates['points_type'] = (int) $data['points_type'];
                    }
                    if ($data['amount'] !== null && $data['amount'] !== '') {
                        $updates['amount'] = (int) $data['amount'];
                    }
                    if ($data['order_number'] !== null && $data['order_number'] !== '') {
                        $updates['order_number'] = (int) $data['order_number'];
                    }
                    if ($data['club_only'] !== null && $data['club_only'] !== '') {
                        $updates['club_only'] = $data['club_only'] === '1' ? '1' : '0';
                    }

                    if (empty($updates)) {
                        Notification::make()->title('Nothing to update')->body('Fill at least one field to apply to the selected items.')->warning()->send();

                        return;
                    }

                    CatalogItem::whereIn('id', $ids)->update($updates);

                    $count = count($ids);
                    $this->resetTable();
                    $this->selectedItemIds = [];

                    Notification::make()->title('Updated items')->body("Applied changes to {$count} item(s).")->success()->send();
                }),
        ];
    }
}
