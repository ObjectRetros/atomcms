<?php

namespace App\Filament\Resources\Hotel\AdaCatalogPages;

use App\Filament\Concerns\RequiresEmulatorDriver;
use App\Filament\Concerns\TranslatesNavigationGroup;
use App\Filament\Resources\Hotel\AdaCatalogPages\Pages\ManageAdaCatalogPages;
use App\Models\Ada\AdaCatalogPage;
use App\Models\Ada\AdaRole;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdaCatalogPageResource extends Resource
{
    use RequiresEmulatorDriver;
    use TranslatesNavigationGroup;

    protected static ?string $model = AdaCatalogPage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected static ?string $navigationLabel = 'Catalog pages';

    protected static ?string $slug = 'hotel/ada-catalog-pages';

    protected static function requiredEmulatorDriver(): string
    {
        return 'ada';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            TextInput::make('caption')->required(),
            TextInput::make('layout')->required(),
            Select::make('catalog_page_id')
                ->label('Parent page')
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => AdaCatalogPage::query()
                    ->where('caption', 'like', "%{$search}%")
                    ->orderBy('caption')
                    ->limit(50)
                    ->pluck('caption', 'id')
                    ->all())
                ->getOptionLabelUsing(fn ($value): ?string => AdaCatalogPage::query()->whereKey($value)->value('caption'))
                ->nullable(),
            // A foreign key into Ada's roles table; there are only a handful,
            // so offer them by name instead of asking for a raw id.
            Select::make('role_id')
                ->label('Minimum role')
                ->options(fn (): array => AdaRole::query()->orderBy('id')->pluck('name', 'id')->all())
                ->searchable()
                ->nullable(),
            TextInput::make('order_id')->integer()->required(),
            TextInput::make('icon_id')->integer()->required(),
            Toggle::make('enabled'),
            Toggle::make('visible'),
            Textarea::make('images_json')->json()->required()->columnSpanFull(),
            Textarea::make('texts_json')->json()->required()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order_id')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('caption')->searchable(),
                TextColumn::make('catalog_page_id')->label('Parent'),
                TextColumn::make('order_id')->sortable(),
                IconColumn::make('enabled')->boolean(),
                IconColumn::make('visible')->boolean(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAdaCatalogPages::route('/')];
    }
}
