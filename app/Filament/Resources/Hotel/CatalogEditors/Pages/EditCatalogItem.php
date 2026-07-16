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
use LogicException;

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

        $this->catalogFormSchema()->fill(CatalogItemFullForm::fillFrom($this->record));

        if ($this->itemBase) {
            $this->itemBaseFormSchema()->fill($this->itemBase->toArray());
        }
    }

    public function getMaxContentWidth(): ?string
    {
        return 'screen-lg';
    }

    public function getTitle(): string
    {
        return $this->record->catalog_name
            ? "Edit: {$this->record->catalog_name}"
            : 'Edit catalog item';
    }

    /** @return array<int, string> */
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
                $data = $this->catalogFormSchema()->getState();
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

                $data = $this->itemBaseFormSchema()->getState();
                $this->itemBase->update($data);

                Notification::make()
                    ->title('items_base saved')
                    ->success()
                    ->send();
            });
    }

    public function getRoomPlacementsProperty(): Collection
    {
        if (! $this->itemBase) {
            return collect();
        }

        // Eager-load room.owner because production disables lazy loading.
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

        $ownerCounts = Item::query()
            ->where('item_id', $this->itemBase->id)
            ->whereNotNull('user_id')
            ->where('user_id', '>', 0)
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(*) as aggregate')
            ->orderByDesc('aggregate')
            ->limit(200)
            ->pluck('aggregate', 'user_id');

        if ($ownerCounts->isEmpty()) {
            return collect();
        }

        $users = User::query()
            ->whereIn('id', $ownerCounts->keys())
            ->get(['id', 'username', 'look', 'rank'])
            ->keyBy('id');

        return $ownerCounts
            ->map(fn ($count, $userId) => [
                'user' => $users->get((int) $userId),
                'count' => (int) $count,
            ])
            ->filter(fn (array $row): bool => $row['user'] !== null)
            ->values();
    }

    public function getIconUrlProperty(): ?string
    {
        if (! $this->itemBase) {
            return null;
        }

        return app(FurniIconService::class)->furniIcon($this->itemBase->item_name);
    }

    private function catalogFormSchema(): Schema
    {
        return $this->getSchema('catalogForm')
            ?? throw new LogicException('The catalog item form schema is not registered.');
    }

    private function itemBaseFormSchema(): Schema
    {
        return $this->getSchema('itemBaseForm')
            ?? throw new LogicException('The item base form schema is not registered.');
    }
}
