<?php

namespace App\Filament\Resources\Hotel;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ChatlogPrivate;
use Filament\Resources\Resource;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Traits\TranslatableResource;
use App\Filament\Resources\Hotel\ChatlogPrivateResource\Pages;

class ChatlogPrivateResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = ChatlogPrivate::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Logs';

    public static string $translateIdentifier = 'chatlog-private';

    protected static ?string $slug = 'hotel/chatlog-private';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('sender')
                    ->disabled()
                    ->formatStateUsing(fn($record) => $record->sender?->username)
                    ->label(__('filament::resources.inputs.sender')),

                TextInput::make('receiver')
                    ->disabled()
                    ->formatStateUsing(fn($record) => $record->receiver?->username)
                    ->label(__('filament::resources.inputs.receiver')),

                Textarea::make('message')
                    ->label(__('filament::resources.inputs.message'))
                    ->columnSpanFull()
                    ->disabled()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('timestamp', 'desc')
            ->columns(self::getTable())
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getTable(): array
    {
        return [
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
            'index' => Pages\ManageChatlogPrivates::route('/'),
        ];
    }
}
