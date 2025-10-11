<?php

namespace App\Filament\Resources\Atom\HousekeepingPermissions;

use App\Filament\Resources\Atom\HousekeepingPermissions\Pages\ListHousekeepingPermissions;
use App\Models\WebsiteHousekeepingPermission;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HousekeepingPermissionResource extends Resource
{
    protected static ?string $model = WebsiteHousekeepingPermission::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $slug = 'website/housekeeping-permissions';

    protected static ?string $navigationLabel = 'Housekeeping permissions';

    public static string $translateIdentifier = 'housekeeping-permissions';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('permission')
                            ->label(__('filament::resources.inputs.permission'))
                            ->maxLength(50)
                            ->autocomplete()
                            ->unique(ignoreRecord: true)
                            ->required(),

                        TextInput::make('min_rank')
                            ->label(__('filament::resources.inputs.min_rank'))
                            ->required()
                            ->maxLength(255)
                            ->autocomplete(),

                        TextInput::make('description')
                            ->label(__('filament::resources.inputs.description'))
                            ->nullable()
                            ->maxLength(255)
                            ->autocomplete()
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'sm' => 2,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'asc')
            ->columns([
                TextColumn::make('permission')
                    ->label(__('filament::resources.columns.permission'))
                    ->searchable(),

                TextColumn::make('min_rank')
                    ->label(__('filament::resources.columns.min_rank'))
                    ->searchable()
                    ->limit(30),

                TextColumn::make('description')
                    ->label(__('filament::resources.columns.description'))
                    ->toggleable()
                    ->searchable()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->limit(60),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
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
            'index' => ListHousekeepingPermissions::route('/'),
        ];
    }
}
