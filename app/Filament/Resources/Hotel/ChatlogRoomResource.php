<?php

namespace App\Filament\Resources\Hotel;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ChatlogRoom;
use Filament\Resources\Resource;
use App\Filament\Traits\TranslatableResource;
use App\Filament\Resources\Hotel\ChatlogRoomResource\Pages;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class ChatlogRoomResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = ChatlogRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Logs';

    public static string $translateIdentifier = 'chatlog-rooms';

    protected static ?string $slug = 'hotel/chatlog-room';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('room')
                    ->label(__('filament::resources.inputs.room'))
                    ->formatStateUsing(fn($record) => $record->room?->name)
                    ->columnSpanFull()
                    ->disabled(),

                TextInput::make('sender')
                    ->label(__('filament::resources.inputs.sender'))
                    ->formatStateUsing(fn($record) => $record->sender?->username)
                    ->disabled(),

                TextInput::make('receiver')
                    ->label(__('filament::resources.inputs.receiver'))
                    ->formatStateUsing(fn($record) => $record->receiver?->username)
                    ->disabled(),

                Textarea::make('message')
                    ->label(__('filament::resources.inputs.message'))
                    ->columnSpanFull()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('timestamp', 'desc')
            ->columns(self::getTable())
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getTable(): array
    {
        return [
            TextColumn::make('room.name')
                ->label(__('filament::resources.columns.room'))
                ->toggleable()
                ->searchable(isIndividual: true),

            TextColumn::make('sender.username')
                ->label(__('filament::resources.columns.sender'))
                ->toggleable()
                ->searchable(isIndividual: true),

            TextColumn::make('receiver.username')
                ->label(__('filament::resources.columns.receiver'))
                ->toggleable()
                ->searchable(isIndividual: true),

            TextColumn::make('message')
                ->label(__('filament::resources.columns.message'))
                ->limit(40)
                ->searchable(isIndividual: true),

            TextColumn::make('timestamp')
                ->label(__('filament::resources.columns.executed_at'))
                ->dateTime('Y-m-d H:i')
                ->toggleable(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageChatlogRooms::route('/'),
        ];
    }
}
