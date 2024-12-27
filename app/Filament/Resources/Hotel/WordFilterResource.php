<?php

namespace App\Filament\Resources\Hotel;

use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Wordfilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Traits\TranslatableResource;
use App\Filament\Resources\Hotel\WordFilterResource\Pages;

class WordFilterResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = Wordfilter::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye-slash';

    protected static ?string $navigationGroup = 'Hotel';

    protected static ?string $slug = 'hotel/wordfilters';

    public static string $translateIdentifier = 'word-filters';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('key')
                    ->label(__('filament::resources.inputs.key'))
                    ->maxLength(256)
                    ->unique('wordfilter', 'key', ignoreRecord: true)
                    ->required(),

                TextInput::make('replacement')
                    ->label(__('filament::resources.inputs.replacement'))
                    ->maxLength(16)
                    ->required(),

                Select::make('hide')
                    ->native(false)
                    ->label(__('filament::resources.inputs.hideable'))
                    ->default('0')
                    ->options([
                        '0' => __('filament::resources.options.no'),
                        '1' => __('filament::resources.options.yes'),
                    ]),

                Select::make('report')
                    ->native(false)
                    ->label(__('filament::resources.inputs.reportable'))
                    ->default('0')
                    ->options([
                        '0' => __('filament::resources.options.no'),
                        '1' => __('filament::resources.options.yes'),
                    ]),

                TextInput::make('mute')
                    ->label(__('filament::resources.inputs.mute_time'))
                    ->columnSpanFull()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(__('filament::resources.columns.key'))
                    ->searchable(),

                TextColumn::make('replacement')
                    ->label(__('filament::resources.columns.replacement'))
                    ->searchable(),

                IconColumn::make('hide')
                    ->label(__('filament::resources.columns.hideable'))
                    ->icon(fn(string $state): string => $state == '0' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->colors([
                        'danger' => '0',
                        'success' => '1',
                    ]),

                IconColumn::make('report')
                    ->label(__('filament::resources.columns.reportable'))
                    ->icon(fn(string $state): string => $state == '0' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->colors([
                        'danger' => '0',
                        'success' => '1',
                    ]),

                TextColumn::make('mute')
                    ->label(__('filament::resources.columns.mute_time'))
                    ->searchable(),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWordFilters::route('/'),
        ];
    }
}
