<?php

namespace App\Filament\Resources\User\Users\RelationManagers;

use App\Filament\Resources\Hotel\ChatlogRooms\ChatlogRoomResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ChatLogRelationManager extends RelationManager
{
    protected static string $relationship = 'chatLogs';

    protected static $targetResource = ChatlogRoomResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table->columns(ChatlogRoomResource::getTable())
            ->defaultSort('timestamp', 'desc');
    }
}
