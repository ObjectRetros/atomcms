<?php

namespace App\Filament\Resources\Hotel\AdaServerLocaleTexts;

use App\Filament\Concerns\RequiresEmulatorDriver;
use App\Filament\Concerns\TranslatesNavigationGroup;
use App\Filament\Resources\Hotel\AdaServerLocaleTexts\Pages\ManageAdaServerLocaleTexts;
use App\Models\Ada\AdaServerLocaleText;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdaServerLocaleTextResource extends Resource
{
    use RequiresEmulatorDriver;
    use TranslatesNavigationGroup;

    protected static ?string $model = AdaServerLocaleText::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-language';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected static ?string $navigationLabel = 'Emulator texts';

    protected static ?string $slug = 'hotel/ada-emulator-texts';

    protected static function requiredEmulatorDriver(): string
    {
        return 'ada';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('key')->required()->maxLength(120)->unique(ignoreRecord: true),
            TextInput::make('text')->required()->maxLength(300),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->searchable(),
                TextColumn::make('text')->searchable(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAdaServerLocaleTexts::route('/')];
    }
}
