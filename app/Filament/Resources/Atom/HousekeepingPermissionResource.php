<?php

namespace App\Filament\Resources\Atom;

use App\Filament\Resources\Atom\HousekeepingPermissionResource\Pages;
use App\Filament\Resources\Atom\HousekeepingPermissionResource\RelationManagers;
use App\Models\HousekeepingPermission;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HousekeepingPermissionResource extends Resource
{
    protected static ?string $model = HousekeepingPermission::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Website';

    protected static ?string $slug = 'website/housekeeping-permissions';

    public static string $translateIdentifier = 'housekeeping-permissions';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
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
                        ->columnSpanFull()
                ])
                ->columns([
                    'sm' => 2
                ])
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

                        if (strlen($state) <= $column->getCharacterLimit()) return null;

                        return $state;
                    })
                    ->limit(60)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListHousekeepingPermissions::route('/'),
        ];
    }
}
