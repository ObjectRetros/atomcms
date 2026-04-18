<?php

namespace App\Filament\Resources\Home;

use App\Enums\CurrencyTypes;
use App\Enums\HomeItemType;
use App\Filament\Resources\Home\HomeItemResource\Pages;
use App\Filament\Traits\TranslatableResource;
use App\Models\Home\HomeItem;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Component;

class HomeItemResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = HomeItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static string|\UnitEnum|null $navigationGroup = 'Profile Management';

    protected static ?string $slug = 'home-management/items';

    public static string $translateIdentifier = 'home-items';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema(static::getForm())
                    ->columns(['sm' => 2]),
            ]);
    }

    public static function getForm(): array
    {
        return [
            Select::make('type')
                ->native(false)
                ->label(__('filament::resources.inputs.type'))
                ->options([
                    's' => __('filament::resources.common.Sticker'),
                    'w' => __('filament::resources.common.Widget'),
                    'n' => __('filament::resources.common.Note'),
                    'b' => __('filament::resources.common.Background'),
                ])
                ->live()
                ->default('s')
                ->required(),

            Select::make('home_category_id')
                ->native(false)
                ->label(__('filament::resources.inputs.category'))
                ->relationship('homeCategory', 'name')
                ->hidden(fn (Get $get): bool => $get('type') !== 's')
                ->nullable(),

            TextInput::make('name')
                ->label(__('filament::resources.inputs.name'))
                ->required()
                ->columnSpanFull()
                ->maxLength(255),

            FileUpload::make('image')
                ->label(__('filament::resources.inputs.image'))
                ->required()
                ->columnSpanFull()
                ->image()
                ->disk('public')
                ->directory('home-items')
                ->visibility('public')
                ->imagePreviewHeight('80'),

            Select::make('currency_type')
                ->native(false)
                ->label(__('filament::resources.inputs.currency_type'))
                ->default(-1)
                ->options(CurrencyTypes::toInput()),

            TextInput::make('price')
                ->label(__('filament::resources.inputs.price'))
                ->required()
                ->numeric(),

            TextInput::make('limit')
                ->numeric()
                ->columnSpanFull()
                ->label(__('filament::resources.inputs.limit'))
                ->helperText(__('filament::resources.helpers.home_item_limit_helper'))
                ->nullable(),

            Toggle::make('enabled')
                ->label(__('filament::resources.inputs.visible'))
                ->default(true),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns(static::getTable())
            ->filters([
                SelectFilter::make('type')
                    ->label(__('filament::resources.columns.type'))
                    ->options([
                        's' => __('filament::resources.common.Sticker'),
                        'w' => __('filament::resources.common.Widget'),
                        'n' => __('filament::resources.common.Note'),
                        'b' => __('filament::resources.common.Background'),
                    ]),

                SelectFilter::make('category')
                    ->label(__('filament::resources.columns.category'))
                    ->relationship('homeCategory', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function getTable(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('filament::resources.columns.id'))
                ->visible(fn (Component $livewire): bool => ! $livewire->isTableReordering),

            TextColumn::make('order')
                ->label(__('filament::resources.columns.order'))
                ->visible(fn (Component $livewire): bool => $livewire->isTableReordering),

            ImageColumn::make('image')
                ->disk('public')
                ->size('auto')
                ->extraImgAttributes(['style' => 'max-width: 200px; max-height: 60px'])
                ->label(__('filament::resources.columns.image')),

            TextColumn::make('name')
                ->label(__('filament::resources.columns.name'))
                ->searchable(),

            TextColumn::make('type')
                ->label(__('filament::resources.columns.type'))
                ->badge()
                ->formatStateUsing(fn (HomeItemType $state): string => match ($state) {
                    HomeItemType::Sticker => __('filament::resources.common.Sticker'),
                    HomeItemType::Widget => __('filament::resources.common.Widget'),
                    HomeItemType::Note => __('filament::resources.common.Note'),
                    HomeItemType::Background => __('filament::resources.common.Background'),
                })
                ->color(fn (HomeItemType $state): string => match ($state) {
                    HomeItemType::Sticker, HomeItemType::Note => 'primary',
                    HomeItemType::Widget => 'success',
                    HomeItemType::Background => 'danger',
                }),

            TextColumn::make('price')
                ->label(__('filament::resources.columns.price'))
                ->searchable(),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeItems::route('/'),
            'create' => Pages\CreateHomeItem::route('/create'),
            'edit' => Pages\EditHomeItem::route('/{record}/edit'),
        ];
    }
}
