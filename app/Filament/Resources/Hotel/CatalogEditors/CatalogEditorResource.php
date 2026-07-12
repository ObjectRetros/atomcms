<?php

namespace App\Filament\Resources\Hotel\CatalogEditors;

use App\Emulator\Data\Feature;
use App\Filament\Concerns\RequiresEmulatorFeature;
use App\Models\Game\Furniture\CatalogPage;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CatalogEditorResource extends Resource
{
    use RequiresEmulatorFeature;

    protected static function requiredEmulatorFeature(): Feature
    {
        return Feature::CatalogManagement;
    }

    protected static ?string $model = CatalogPage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected static ?string $navigationLabel = 'Catalog Editor';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('caption')->label('Page Name')->searchable(),
                TextColumn::make('parent_id')->label('Parent ID'),
                TextColumn::make('order_num')->label('Order'),
                IconColumn::make('visible')->boolean()->label('Visible'),
                IconColumn::make('enabled')->boolean()->label('Enabled'),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCatalogEditor::route('/'),
            'edit-item' => Pages\EditCatalogItem::route('/items/{item}/edit'),
        ];
    }
}
