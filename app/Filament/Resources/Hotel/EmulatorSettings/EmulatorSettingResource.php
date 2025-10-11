<?php

namespace App\Filament\Resources\Hotel\EmulatorSettings;

use App\Filament\Resources\Hotel\EmulatorSettings\Pages\CreateEmulatorSetting;
use App\Filament\Resources\Hotel\EmulatorSettings\Pages\EditEmulatorSetting;
use App\Filament\Resources\Hotel\EmulatorSettings\Pages\ListEmulatorSettings;
use App\Filament\Traits\TranslatableResource;
use App\Models\EmulatorSetting;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmulatorSettingResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = EmulatorSetting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    public static string $translateIdentifier = 'emulator-settings';

    protected static ?string $slug = 'hotel/emulator-settings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('key')
                            ->label(__('filament::resources.inputs.key'))
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                        TextInput::make('value')
                            ->label(__('filament::resources.inputs.value'))
                            ->required()
                            ->maxLength(512),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(__('filament::resources.columns.key'))
                    ->searchable(),

                TextColumn::make('value')
                    ->label(__('filament::resources.columns.value'))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmulatorSettings::route('/'),
            'create' => CreateEmulatorSetting::route('/create'),
            'edit' => EditEmulatorSetting::route('/{record}/edit'),
        ];
    }
}
