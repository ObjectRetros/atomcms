<?php

namespace App\Filament\Resources\Hotel\CatalogEditors\Tables;

use App\Filament\Resources\Hotel\CatalogEditors\CatalogEditorResource;
use App\Filament\Resources\Hotel\CatalogEditors\Forms\CatalogItemMassEditForm;
use App\Models\Game\Furniture\CatalogItem;
use App\Models\Game\Furniture\CatalogPage;
use App\Services\Catalog\CatalogReorderService;
use App\Services\Catalog\FurniIconService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;

class CatalogItemsTable
{
    /** @param  callable(): ?int  $selectedPageId  read lazily so the table sees fresh Livewire state */
    public static function configure(Table $table, callable $selectedPageId): Table
    {
        $icons = app(FurniIconService::class);
        $reorder = app(CatalogReorderService::class);

        return $table
            ->reorderable('order_number')
            ->defaultSort('order_number')
            ->paginated(false)
            ->columns(self::columns($icons))
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (CatalogItem $record) => CatalogEditorResource::getUrl('edit-item', ['item' => $record->id]))
                    ->extraAttributes(['wire:navigate' => true]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    self::massEditAction(),
                    self::moveToPageAction($reorder),
                ]),
            ])
            ->emptyStateHeading('No items on this page')
            ->emptyStateDescription('Drag items here from another page, or open the housekeeping catalog import.')
            ->emptyStateIcon('heroicon-o-rectangle-stack');
    }

    /** @return array<int, Tables\Columns\Column> */
    private static function columns(FurniIconService $icons): array
    {
        return [
            Tables\Columns\ImageColumn::make('icon')
                ->label('')
                ->width(36)
                ->height(36)
                ->extraImgAttributes(['style' => 'image-rendering: pixelated'])
                ->getStateUsing(fn (CatalogItem $r) => $icons->furniIcon($r->catalog_name)),

            Tables\Columns\TextColumn::make('catalog_name')
                ->label('Item')
                ->searchable()
                ->wrap()
                ->description(fn (CatalogItem $r) => 'ID '.$r->id),

            Tables\Columns\TextColumn::make('cost_credits')->label('Credits')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('cost_points')->label('Points')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('points_type')->label('Type')->numeric()->sortable()->toggleable(),
            Tables\Columns\TextColumn::make('amount')->label('Qty')->numeric()->sortable(),

            Tables\Columns\IconColumn::make('club_only')
                ->boolean()
                ->label('HC')
                ->getStateUsing(fn (CatalogItem $r) => $r->club_only === '1'),

            Tables\Columns\TextColumn::make('order_number')
                ->label('Order')
                ->numeric()
                ->toggleable()
                ->badge()
                ->color(fn (CatalogItem $r) => $r->order_number === -1 ? 'danger' : 'gray'),
        ];
    }

    private static function massEditAction(): BulkAction
    {
        return BulkAction::make('massEdit')
            ->label('Mass edit')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('Mass edit selected items')
            ->modalSubmitActionLabel('Apply changes')
            ->modalWidth('lg')
            ->form(CatalogItemMassEditForm::schema())
            ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                $updates = CatalogItemMassEditForm::pickUpdates($data);

                if (empty($updates)) {
                    Notification::make()
                        ->title('Nothing to update')
                        ->body('Fill at least one field.')
                        ->warning()
                        ->send();

                    return;
                }

                CatalogItem::whereIn('id', $records->pluck('id'))->update($updates);

                Notification::make()
                    ->title('Updated '.$records->count().' item(s)')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    private static function moveToPageAction(CatalogReorderService $reorder): BulkAction
    {
        return BulkAction::make('moveToPage')
            ->label('Move to page')
            ->icon('heroicon-m-arrows-right-left')
            ->modalHeading('Move selected items to another page')
            ->modalSubmitActionLabel('Move')
            ->modalWidth('md')
            ->form([
                Forms\Components\Select::make('page_id')
                    ->label('Destination page')
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => CatalogPage::query()
                        ->where('caption', 'like', "%{$search}%")
                        ->orderBy('caption')
                        ->limit(50)
                        ->pluck('caption', 'id'))
                    ->getOptionLabelUsing(fn ($value) => CatalogPage::find($value)?->caption),
            ])
            ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) use ($reorder): void {
                $moved = $reorder->moveItemsToPage((int) $data['page_id'], $records->pluck('id')->all());

                Notification::make()
                    ->title("Moved {$moved} item(s)")
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
