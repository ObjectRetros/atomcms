<?php

namespace App\Filament\Resources\User\Users\RelationManagers;

use App\Contracts\Rcon;
use App\Filament\Tables\Columns\HabboBadgeColumn;
use App\Filament\Traits\TranslatableResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BadgesRelationManager extends RelationManager
{
    use TranslatableResource;

    protected static string $relationship = 'badges';

    protected static ?string $recordTitleAttribute = 'badge_code';

    protected static ?string $translateIdentifier = 'badges';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('badge_code')
                    ->label(__('filament::resources.inputs.badge_code'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest('id'))
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament::resources.columns.id')),

                HabboBadgeColumn::make('badge')
                    ->alignCenter()
                    ->label(__('filament::resources.columns.image')),

                TextColumn::make('badge_code')
                    ->label(__('filament::resources.columns.badge_code'))
                    ->searchable(),

                IconColumn::make('slot_id')
                    ->label(__('filament::resources.columns.equipped'))
                    ->icon(fn ($record) => $record->slot_id > 0 ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->colors([
                        'success' => fn (string $state) => $state > 0,
                        'danger' => fn (string $state) => $state <= 0,
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->before(function (CreateAction $action, RelationManager $livewire): void {
                        $user = $livewire->getOwnerRecord();

                        if (! $user->online) {
                            return;
                        }

                        // Online users must receive badges through the emulator;
                        // inserting the row directly would desync their session.
                        $rcon = app(Rcon::class);

                        if ($rcon->isConnected()) {
                            $rcon->giveBadge($user, $action->getFormData()['badge_code']);

                            Notification::make()
                                ->success()
                                ->title('Badge sent through the emulator')
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('RCON is not connected!')
                                ->body("You can't send badges to online users while RCON is unavailable.")
                                ->persistent()
                                ->send();
                        }

                        $action->cancel();
                    }),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->before(fn (DeleteAction $action, RelationManager $livewire) => self::onDeleteBadgeAction($action, $livewire)),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->before(fn (DeleteBulkAction $action, RelationManager $livewire) => self::onDeleteBadgeAction($action, $livewire)),
            ]);
    }

    public static function onDeleteBadgeAction(DeleteAction|DeleteBulkAction $action, RelationManager $livewire): void
    {
        $user = $livewire->getOwnerRecord();

        if (! $user->online) {
            return;
        }

        // The emulator has no remove-badge command, and deleting rows under an
        // online user desyncs their session.
        Notification::make()
            ->danger()
            ->title('User is online')
            ->body('Badges can only be removed while the user is offline.')
            ->persistent()
            ->send();

        $action->cancel();
    }
}
