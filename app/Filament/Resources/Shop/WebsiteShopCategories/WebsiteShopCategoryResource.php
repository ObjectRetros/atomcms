<?php

namespace App\Filament\Resources\Shop\WebsiteShopCategories;

use App\Filament\Concerns\TranslatableResource;
use App\Models\Shop\WebsiteShopCategory;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebsiteShopCategoryResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = WebsiteShopCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Shop Management';

    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'shop/categories';

    public static string $translateIdentifier = 'shop-categories';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('icon')
                            ->maxLength(255),

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->default(true)
                            ->helperText('Inactive categories will not be shown in the shop'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->sortable(),

                Tables\Columns\TextColumn::make('packages_count')
                    ->counts('packages')
                    ->label('Packages'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All Categories')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->before(function (Actions\DeleteAction $action, WebsiteShopCategory $record): void {
                        if ($record->packages()->exists()) {
                            self::notifyHasPackages();

                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->before(function (Actions\DeleteBulkAction $action, Collection $records): void {
                            if ($records->contains(fn (Model $record): bool => $record instanceof WebsiteShopCategory && $record->packages()->exists())) {
                                self::notifyHasPackages();

                                $action->cancel();
                            }
                        }),
                ]),
            ]);
    }

    private static function notifyHasPackages(): void
    {
        Notification::make()
            ->title('Cannot delete a category that still has packages')
            ->body('Move or delete its packages first.')
            ->danger()
            ->send();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PackagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebsiteShopCategories::route('/'),
            'create' => Pages\CreateWebsiteShopCategory::route('/create'),
            'edit' => Pages\EditWebsiteShopCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $activeCategories = Cache::remember(
            'housekeeping.shop-categories.active-count',
            300,
            fn (): int => static::getModel()::where('is_active', true)->count(),
        );

        return $activeCategories > 0 ? (string) $activeCategories : null;
    }
}
