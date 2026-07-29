<?php

namespace App\Filament\Resources\User\Users\RelationManagers;

use App\Emulator\Contracts\BadgeRepository;
use App\Filament\Concerns\TranslatableResource;
use App\Models\Ada\AdaPlayerBadge;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * Ada normalises badges over player_badges and badges, so grants and revokes
 * go through the driver rather than the relation. The Arcturus counterpart is
 * BadgesRelationManager.
 */
class AdaBadgesRelationManager extends RelationManager
{
    use TranslatableResource;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static string $relationship = 'emulatorBadges';

    protected static ?string $translateIdentifier = 'badges';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('badge_code')
                ->label(__('filament::resources.inputs.badge_code'))
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
        ]);
    }

    public function getRecordTitle(?Model $record = null): string
    {
        return $record instanceof AdaPlayerBadge
            ? (string) $record->badge?->getAttribute('code')
            : '';
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('badge')->latest('id'))
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament::resources.columns.id')),

                TextColumn::make('badge.code')
                    ->label(__('filament::resources.columns.badge_code'))
                    ->searchable(),

                IconColumn::make('slot')
                    ->label(__('filament::resources.columns.equipped'))
                    ->icon(fn (AdaPlayerBadge $record): string => $record->slot > 0 ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (AdaPlayerBadge $record): string => $record->slot > 0 ? 'success' : 'danger'),
            ])
            ->headerActions([
                CreateAction::make()->before(function (CreateAction $action): void {
                    $user = $this->owner();

                    $this->warnWhenOnline($user);

                    app(BadgeRepository::class)->grant($user, (string) $action->getFormData()['badge_code']);

                    $action->cancel();
                }),
            ])
            ->recordActions([
                DeleteAction::make()->before(function (DeleteAction $action): void {
                    $record = $action->getRecord();
                    $code = $record instanceof AdaPlayerBadge
                        ? $record->badge?->getAttribute('code')
                        : null;

                    if (is_string($code)) {
                        $user = $this->owner();

                        $this->warnWhenOnline($user);

                        app(BadgeRepository::class)->revoke($user, $code);
                    }

                    $action->cancel();
                }),
            ])
            ->toolbarActions([]);
    }

    /**
     * Ada exposes no RCON, so the badge is written straight into the live
     * database. Say so rather than letting a session silently drift.
     */
    private function warnWhenOnline(User $user): void
    {
        if (! $user->online) {
            return;
        }

        Notification::make()
            ->warning()
            ->title(__('User is online'))
            ->body(__('Ada has no RCON bridge, so this change only reaches the player after they reconnect.'))
            ->send();
    }

    private function owner(): User
    {
        $record = $this->getOwnerRecord();

        if (! $record instanceof User) {
            throw new LogicException('The badge manager received an unsupported owner model.');
        }

        return $record;
    }
}
