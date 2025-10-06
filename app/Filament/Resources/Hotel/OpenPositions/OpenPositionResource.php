<?php

namespace App\Filament\Resources\Hotel\OpenPositions;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Hotel\OpenPositions\Pages\ListOpenPositions;
use App\Filament\Resources\Hotel\OpenPositions\Pages\CreateOpenPosition;
use App\Filament\Resources\Hotel\OpenPositions\Pages\EditOpenPosition;
use App\Filament\Resources\Hotel\OpenPositionResource\Pages;
use App\Models\Community\Staff\WebsiteOpenPosition;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class OpenPositionResource extends Resource
{
    protected static ?string $model = WebsiteOpenPosition::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static string | \UnitEnum | null $navigationGroup = 'Hotel';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('permission_id')
                    ->label('Rank')
                    ->relationship('permission', 'rank_name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a rank')
                    ->rules([
                        'required',
                        function () {
                            return function ($attribute, $value, $fail) {
                                $existingPosition = WebsiteOpenPosition::where('permission_id', $value)
                                    ->where('id', '!=', request()->route('record') ?? 0)
                                    ->exists();

                                if ($existingPosition) {
                                    $fail('This rank is already used in an open position.');
                                }
                            };
                        },
                    ]),
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
                TextColumn::make('permission.rank_name')
                    ->label('Rank')
                    ->sortable()
                    ->searchable(),
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
            ->filters([
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Open Position')
                    ->modalDescription('This will also delete all related staff applications. Are you sure?')
                    ->modalSubmitActionLabel('Yes, delete')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Open Position Deleted')
                            ->body('The open position and its related staff applications have been deleted successfully.')
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Open Positions')
                    ->modalDescription('This will also delete all related staff applications for the selected positions. Are you sure?')
                    ->modalSubmitActionLabel('Yes, delete')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Open Positions Deleted')
                            ->body('The selected open positions and their related staff applications have been deleted successfully.')
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