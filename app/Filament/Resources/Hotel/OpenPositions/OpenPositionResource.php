<?php

namespace App\Filament\Resources\Hotel\OpenPositions;

use App\Filament\Resources\Hotel\OpenPositions\Pages\CreateOpenPosition;
use App\Filament\Resources\Hotel\OpenPositions\Pages\EditOpenPosition;
use App\Filament\Resources\Hotel\OpenPositions\Pages\ListOpenPositions;
use App\Models\Community\Staff\WebsiteOpenPosition;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OpenPositionResource extends Resource
{
    protected static ?string $model = WebsiteOpenPosition::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ToggleButtons::make('position_kind')
                    ->label('Type')
                    ->inline()
                    ->options([
                        'rank' => 'Ranks',
                        'team' => 'Teams',
                    ])
                    ->default('rank')
                    ->required()
                    ->live()
                    ->grouped(),

                Select::make('permission_id')
                    ->label('Rank')
                    ->relationship('permission', 'rank_name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a rank')
                    ->visible(fn (Get $get) => $get('position_kind') === 'rank')
                    ->required(fn (Get $get) => $get('position_kind') === 'rank')
                    ->dehydrated(fn (Get $get) => $get('position_kind') === 'rank')
                    ->unique(
                        table: WebsiteOpenPosition::class,
                        column: 'permission_id',
                        ignoreRecord: true,
                    ),

                Select::make('team_id')
                    ->label('Team')
                    ->relationship('team', 'rank_name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a team')
                    ->visible(fn (Get $get) => $get('position_kind') === 'team')
                    ->required(fn (Get $get) => $get('position_kind') === 'team')
                    ->dehydrated(fn (Get $get) => $get('position_kind') === 'team')
                    ->unique(
                        table: WebsiteOpenPosition::class,
                        column: 'team_id',
                        ignoreRecord: true,
                    ),

                Textarea::make('description')
                    ->label('Position Description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),

                DateTimePicker::make('apply_from')
                    ->label('Application Start Date')
                    ->nullable(),

                DateTimePicker::make('apply_to')
                    ->label('Application End Date')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('position_kind')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->sortable(),

                TextColumn::make('permission.rank_name')
                    ->label('Rank')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('team.rank_name')
                    ->label('Team')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('apply_from')
                    ->label('Apply From')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('apply_to')
                    ->label('Apply To')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Open Position')
                    ->modalDescription('This will also delete related rank-based staff applications (if any). Are you sure?')
                    ->modalSubmitActionLabel('Yes, delete')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Open Position Deleted')
                            ->body('The open position and its related staff applications (if rank-based) have been deleted successfully.'),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Open Positions')
                    ->modalDescription('This will also delete related rank-based staff applications (if any). Are you sure?')
                    ->modalSubmitActionLabel('Yes, delete')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Open Positions Deleted')
                            ->body('The selected open positions and their related applications (if rank-based) have been deleted successfully.'),
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOpenPositions::route('/'),
            'create' => CreateOpenPosition::route('/create'),
            'edit' => EditOpenPosition::route('/{record}/edit'),
        ];
    }
}
