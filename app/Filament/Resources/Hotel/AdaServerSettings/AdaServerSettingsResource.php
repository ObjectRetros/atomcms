<?php

namespace App\Filament\Resources\Hotel\AdaServerSettings;

use App\Filament\Concerns\RequiresEmulatorDriver;
use App\Filament\Concerns\TranslatesNavigationGroup;
use App\Filament\Resources\Hotel\AdaServerSettings\Pages\ManageAdaServerSettings;
use App\Models\Ada\AdaServerSettings;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdaServerSettingsResource extends Resource
{
    use RequiresEmulatorDriver;
    use TranslatesNavigationGroup;

    protected static ?string $model = AdaServerSettings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected static ?string $navigationLabel = 'Emulator settings';

    protected static ?string $slug = 'hotel/ada-emulator-settings';

    protected static function requiredEmulatorDriver(): string
    {
        return 'ada';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('player_welcome_message')->columnSpanFull(),
            Toggle::make('fair_currency_rewards'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('player_welcome_message')->wrap(),
                IconColumn::make('fair_currency_rewards')->boolean(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAdaServerSettings::route('/')];
    }
}
