<?php

namespace App\Filament\Resources\Hotel\AdaCatalogItems;

use App\Filament\Concerns\RequiresEmulatorDriver;
use App\Filament\Concerns\TranslatesNavigationGroup;
use App\Filament\Resources\Hotel\AdaCatalogItems\Pages\ManageAdaCatalogItems;
use App\Models\Ada\AdaCatalogItem;
use App\Models\Ada\AdaCatalogPage;
use App\Models\Ada\AdaFurnitureItem;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdaCatalogItemResource extends Resource
{
    use RequiresEmulatorDriver;
    use TranslatesNavigationGroup;

    protected static ?string $model = AdaCatalogItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected static ?string $navigationLabel = 'Catalog items';

    protected static ?string $slug = 'hotel/ada-catalog-items';

    protected static function requiredEmulatorDriver(): string
    {
        return 'ada';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            // A stocked hotel carries thousands of pages and tens of thousands
            // of furniture definitions, so both of these search server-side
            // rather than rendering the whole table into the form.
            Select::make('catalog_page_id')
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => AdaCatalogPage::query()
                    ->where('caption', 'like', "%{$search}%")
                    ->orderBy('caption')
                    ->limit(50)
                    ->pluck('caption', 'id')
                    ->all())
                ->getOptionLabelUsing(fn ($value): ?string => AdaCatalogPage::query()->whereKey($value)->value('caption'))
                ->required(),
            TextInput::make('cost_credits')->integer()->required(),
            TextInput::make('cost_points')->integer()->required(),
            TextInput::make('cost_points_type')->integer()->required(),
            Toggle::make('requires_club_membership'),
            TextInput::make('amount')->integer()->required(),
            TextInput::make('stack_limit')->integer()->required(),
            TextInput::make('sell_limit')->integer()->required(),
            Textarea::make('meta_data')->columnSpanFull(),
            Select::make('furniture')
                ->relationship('furniture', 'name')
                ->getOptionLabelFromRecordUsing(fn (AdaFurnitureItem $record): string => sprintf(
                    '%d: %s',
                    $record->getKey(),
                    $record->getAttribute('name'),
                ))
                ->multiple()
                ->searchable()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('page.caption')->label('Page')->searchable(),
                TextColumn::make('cost_credits')->sortable(),
                TextColumn::make('cost_points')->sortable(),
                TextColumn::make('amount'),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAdaCatalogItems::route('/')];
    }
}
