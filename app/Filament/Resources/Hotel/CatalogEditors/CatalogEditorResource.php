<?php

namespace App\Filament\Resources\Hotel\CatalogEditors;

use App\Emulator\Data\Feature;
use App\Filament\Concerns\RequiresEmulatorFeature;
use App\Filament\Concerns\TranslatableResource;
use App\Models\Game\Furniture\CatalogPage;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

class CatalogEditorResource extends Resource
{
    use RequiresEmulatorFeature;
    use TranslatableResource;

    protected static function requiredEmulatorFeature(): Feature
    {
        return Feature::CatalogManagement;
    }

    protected static ?string $model = CatalogPage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    public static string $translateIdentifier = 'catalog-editor';

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
