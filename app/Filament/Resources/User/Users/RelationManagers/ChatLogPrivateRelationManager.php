<?php

namespace App\Filament\Resources\User\Users\RelationManagers;

use App\Filament\Resources\Hotel\ChatlogPrivates\ChatlogPrivateResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ChatLogPrivateRelationManager extends RelationManager
{
    protected static string $relationship = 'chatLogsPrivate';

    protected static $targetResource = ChatlogPrivateResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table->columns(ChatlogPrivateResource::getTable())
            ->defaultSort('timestamp', 'desc');
    }
}
