<?php

namespace App\Filament\Resources\Hotel;

use App\Filament\Resources\Hotel\OpenPositionResource\Pages;
use App\Models\OpenPosition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class OpenPositionResource extends Resource
{
    protected static ?string $model = OpenPosition::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Hotel';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('permission_id')
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
                                $existingPosition = \App\Models\OpenPosition::where('permission_id', $value)
                                    ->where('id', '!=', request()->route('record') ?? 0)
                                    ->exists();

                                if ($existingPosition) {
                                    $fail('This rank is already used in an open position.');
                                }
                            };
                        },
                    ]),
                Forms\Components\Textarea::make('description')
                    ->label('Position Description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('apply_from')
                    ->label('Application Start Date')
                    ->nullable(),
                Forms\Components\DateTimePicker::make('apply_to')
                    ->label('Application End Date')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('permission.rank_name')
                    ->label('Rank')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('apply_from')
                    ->label('Apply From')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('apply_to')
                    ->label('Apply To')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
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
            'index' => Pages\ListOpenPositions::route('/'),
            'create' => Pages\CreateOpenPosition::route('/create'),
            'edit' => Pages\EditOpenPosition::route('/{record}/edit'),
        ];
    }
}