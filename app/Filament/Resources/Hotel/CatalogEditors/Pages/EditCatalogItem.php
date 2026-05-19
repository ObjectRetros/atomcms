<?php

namespace App\Filament\Resources\Hotel\CatalogEditors\Pages;

use App\Filament\Resources\Hotel\CatalogEditors\CatalogEditorResource;
use App\Filament\Resources\Hotel\CatalogEditors\Forms\CatalogItemFullForm;
use App\Filament\Resources\Hotel\CatalogEditors\Forms\ItemBaseForm;
use App\Models\Game\Furniture\CatalogItem;
use App\Models\Game\Furniture\Item;
use App\Models\Game\Furniture\ItemBase;
use App\Models\Game\Room;
use App\Models\User;
use App\Services\Catalog\FurniIconService;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

/**
 * Dedicated edit page for one catalog_items row.
 *
 * Renders four tabs:
 *   1. Catalog item     - every editable column on catalog_items
 *   2. Items base       - the underlying items_base row (the furniture
 *                         definition this catalog row points at)
 *   3. Placed in rooms  - distinct rooms that hold an instance of this
 *                         items_base id, with owner + count per room
 *   4. Owners           - users who own at least one instance, with their
 *                         total count
 *
 * All relations are eager-loaded server-side so production lazy-loading
 * prevention doesn't trip.
 */
class EditCatalogItem extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = CatalogEditorResource::class;

    protected string $view = 'filament.resources.hotel.catalog-editors.pages.edit-catalog-item';

    public CatalogItem $record;

    public ?ItemBase $itemBase = null;

    public array $catalogData = [];

    public array $itemBaseData = [];

    public function mount(int $item): void
    {
        $this->record = CatalogItem::findOrFail($item);
        $this->itemBase = ItemBase::query()
            ->whereKey((int) $this->record->item_ids)
            ->first();

        $this->catalogForm->fill(CatalogItemFullForm::fillFrom($this->record));

        if ($this->itemBase) {
            $this->itemBaseForm->fill($this->itemBase->toArray());
        }
    }

    public function getMaxContentWidth(): ?string
    {
        return 'screen-lg';
    }

    public function getTitle(): string
    {
        return $this->record?->catalog_name
            ? "Edit: {$this->record->catalog_name}"
            : 'Edit catalog item';
    }

    /* ----- Forms -------------------------------------------------------- */

    /**
     * @return array<int, string>
     */
    protected function getForms(): array
    {
        return ['catalogForm', 'itemBaseForm'];
    }

    public function catalogForm(Schema $schema): Schema
    {
        return $schema
            ->components(CatalogItemFullForm::schema())
            ->statePath('catalogData');
    }

    public function itemBaseForm(Schema $schema): Schema
    {
        return $schema
            ->components(ItemBaseForm::schema())
            ->statePath('itemBaseData');
    }

    /* ----- Save actions -------------------------------------------------- */

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to catalog')
                ->icon('heroicon-m-arrow-uturn-left')
                ->color('gray')
                ->url(fn () => CatalogEditorResource::getUrl('index'))
                ->extraAttributes(['wire:navigate' => true]),
        ];
    }

    public function saveCatalogAction(): Action
    {
        return Action::make('saveCatalog')
            ->label('Save catalog item')
            ->icon('heroicon-m-check')
            ->action(function (): void {
                $data = $this->catalogForm->getState();
                $this->record->update(CatalogItemFullForm::castForSave($data));

                Notification::make()
                    ->title('Catalog item saved')
                    ->success()
                    ->send();
            });
    }

    public function saveItemBaseAction(): Action
    {
        return Action::make('saveItemBase')
            ->label('Save items_base')
            ->icon('heroicon-m-check')
            ->visible(fn () => (bool) $this->itemBase)
            ->action(function (): void {
                if (! $this->itemBase) {
                    return;
                }

                $data = $this->itemBaseForm->getState();
                $this->itemBase->update($data);

                Notification::make()
                    ->title('items_base saved')
                    ->success()
                    ->send();
            });
    }

    /* ----- Tab data (rooms / owners) ------------------------------------ */

    public function getRoomPlacementsProperty(): Collection
    {
        if (! $this->itemBase) {
            return collect();
        }

        // Items eager-load room.owner so the view can render owner + room
        // info without a single lazy query.
        return Item::query()
            ->where('item_id', $this->itemBase->id)
            ->whereNotNull('room_id')
            ->where('room_id', '>', 0)
            ->with(['room.owner:id,username,look'])
            ->get(['id', 'item_id', 'room_id', 'user_id'])
            ->groupBy('room_id')
            ->map(fn (Collection $items, int $roomId) => [
                'room' => $items->first()->room,
                'count' => $items->count(),
            ])
            ->values();
    }

    public function getOwnerSummaryProperty(): Collection
    {
        if (! $this->itemBase) {
            return collect();
        }

        $userIds = Item::query()
            ->where('item_id', $this->itemBase->id)
            ->whereNotNull('user_id')
            ->where('user_id', '>', 0)
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(*) as total')
            ->orderByDesc('total')
            ->limit(200)
            ->get();

        if ($userIds->isEmpty()) {
            return collect();
        }

        $users = User::query()
            ->whereIn('id', $userIds->pluck('user_id'))
            ->get(['id', 'username', 'look', 'rank'])
            ->keyBy('id');

        return $userIds
            ->map(fn ($row) => [
                'user' => $users->get($row->user_id),
                'count' => (int) $row->total,
            ])
            ->filter(fn ($r) => $r['user'])
            ->values();
    }

    public function getIconUrlProperty(): ?string
    {
        if (! $this->itemBase) {
            return null;
        }

        return app(FurniIconService::class)->furniIcon($this->itemBase->item_name);
    }
}
