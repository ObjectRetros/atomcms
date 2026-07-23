<?php

namespace App\Filament\Resources\Hotel\PlusChatlogs;

use App\Filament\Concerns\RequiresEmulatorDriver;
use App\Filament\Concerns\TranslatableResource;
use App\Filament\Resources\Hotel\PlusChatlogs\Pages\ManagePlusChatlogs;
use App\Models\Plus\PlusChatlog;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Read-only room chatlog viewer on the Plus EMU schema; the Arcturus
 * counterpart is ChatlogRoomResource.
 */
class PlusChatlogResource extends Resource
{
    use RequiresEmulatorDriver;
    use TranslatableResource;

    protected static ?string $model = PlusChatlog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Logs';

    public static string $translateIdentifier = 'chatlog-rooms';

    protected static ?string $slug = 'hotel/plus-chatlogs';

    protected static function requiredEmulatorDriver(): string
    {
        return 'plus';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('room_id')
                    ->label(__('filament::resources.inputs.room'))
                    ->disabled(),

                TextInput::make('sender')
                    ->label(__('filament::resources.inputs.sender'))
                    ->formatStateUsing(fn ($record) => $record->user?->username)
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
            ->columns([
                TextColumn::make('room_id')
                    ->label(__('filament::resources.columns.room'))
                    ->toggleable()
                    ->searchable(isIndividual: true),

                TextColumn::make('user.username')
                    ->label(__('filament::resources.columns.sender'))
                    ->toggleable()
                    ->searchable(isIndividual: true),

                TextColumn::make('message')
                    ->label(__('filament::resources.columns.message'))
                    ->limit(40)
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('timestamp')
                    ->label(__('filament::resources.columns.executed_at'))
                    ->formatStateUsing(fn ($state): string => $state ? date('Y-m-d H:i', (int) $state) : '-')
                    ->toggleable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user:id,username,look');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePlusChatlogs::route('/'),
        ];
    }
}
