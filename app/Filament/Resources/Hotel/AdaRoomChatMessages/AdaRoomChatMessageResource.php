<?php

namespace App\Filament\Resources\Hotel\AdaRoomChatMessages;

use App\Filament\Concerns\RequiresEmulatorDriver;
use App\Filament\Concerns\TranslatesNavigationGroup;
use App\Filament\Resources\Hotel\AdaRoomChatMessages\Pages\ManageAdaRoomChatMessages;
use App\Models\Ada\AdaRoomChatMessage;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdaRoomChatMessageResource extends Resource
{
    use RequiresEmulatorDriver;
    use TranslatesNavigationGroup;

    protected static ?string $model = AdaRoomChatMessage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Logs';

    protected static ?string $navigationLabel = 'Room chatlogs';

    protected static ?string $slug = 'hotel/ada-room-chatlogs';

    protected static function requiredEmulatorDriver(): string
    {
        return 'ada';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('room.name')->disabled(),
            TextInput::make('player.username')->disabled(),
            Textarea::make('message')->disabled()->columnSpanFull(),
            TextInput::make('created_at')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('room.name')->label(__('filament::resources.columns.room'))->searchable(),
                TextColumn::make('player.username')->label(__('filament::resources.columns.sender'))->searchable(),
                TextColumn::make('message')->limit(60)->searchable(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->recordActions([ViewAction::make()])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAdaRoomChatMessages::route('/')];
    }
}
