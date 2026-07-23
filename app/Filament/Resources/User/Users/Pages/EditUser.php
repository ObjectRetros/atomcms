<?php

namespace App\Filament\Resources\User\Users\Pages;

use App\Actions\SendCurrency;
use App\Contracts\Rcon;
use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Data\Feature;
use App\Emulator\Emulator;
use App\Enums\CurrencyTypes;
use App\Filament\Resources\User\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use LogicException;

class EditUser extends EditRecord
{
    /**
     * Columns that are guarded against mass assignment on the User model. The
     * housekeeping editor may still change them once beforeSave() has verified
     * the actor's authority, so they are persisted through forceFill().
     */
    private const PRIVILEGED_ATTRIBUTES = ['rank', 'credits', 'hidden_staff', 'extra_rank', 'auth_ticket', 'team_id'];

    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return static::$resource::fillWithOutsideData(
            $this->getRecord(),
            $data,
        );
    }

    /**
     * @throws Halt
     */
    protected function beforeSave(): void
    {
        $user = $this->userRecord($this->getRecord());
        $data = $this->getSchema('form')?->getState()
            ?? throw new LogicException('The user edit form schema is not registered.');
        $actor = auth()->user();

        if (! $actor instanceof User || $actor->cannot('update', $user) || (int) $data['rank'] >= $actor->rank) {
            Notification::make()
                ->danger()
                ->title(__('You cannot edit this user!'))
                ->body(__('You cannot edit users with a higher rank than yours.'))
                ->send();

            $this->halt();
        }

        if ($user->online && ! app(Rcon::class)->isConnected()) {
            Notification::make()
                ->danger()
                ->title(__('RCON is not enabled!'))
                ->body(__('You cannot edit users because RCON is not enabled and the user is online.'))
                ->send();

            $this->halt();
        }
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = $this->userRecord($record);
        $rcon = app(Rcon::class);

        return DB::transaction(function () use ($user, $data, $rcon): Model {
            if (! $user->online) {
                $this->treatChangedCurrenciesWithoutRcon($user, $data);

                return $this->persistUserUpdate($user, $data);
            }

            if ($data['credits'] != $user->credits) {
                app(SendCurrency::class)->execute($user, CurrencyTypes::Credits, -$user->credits + $data['credits']);
            }

            $this->checkUsernameChangedPermission($user, $data, $rcon);
            $this->treatChangedCurrencies($user, $data);
            $this->treatChangedUserRank($user, $data, $rcon);
            $this->treatChangedUserMotto($user, $data, $rcon);

            // The emulator persists these fields when the deferred RCON commands run.
            return $this->persistUserUpdate($user, Arr::except($data, ['credits', 'rank', 'motto']));
        });
    }

    /**
     * Persists a housekeeping edit, force-filling the attributes the User
     * model guards against mass assignment. beforeSave() has already
     * authorized the actor for these privileged changes.
     *
     * @param  array<string, mixed>  $data
     */
    private function persistUserUpdate(User $user, array $data): User
    {
        $user->fill(Arr::except($data, self::PRIVILEGED_ATTRIBUTES));
        $user->forceFill(Arr::only($data, self::PRIVILEGED_ATTRIBUTES));
        $user->save();

        return $user;
    }

    /** @param array<string, mixed> $data */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! hasHousekeepingPermission('reset_user_password', auth()->user())) {
            unset($data['password']);
        }

        return $data;
    }

    /** @param array<string, mixed> $data */
    private function treatChangedCurrenciesWithoutRcon(User $user, array $data): void
    {
        $currencies = app(CurrencyRepository::class);

        foreach ($this->changedCurrencies($user, $data) as [$type, $current, $updated]) {
            $currencies->give($user, $type, $updated - $current);

            activity()
                ->performedOn($user)
                ->withProperties(['old_amount' => $current, 'new_amount' => $updated, 'user_id' => $user->id, 'type' => $type->value])
                ->event('updated')
                ->log("Currency updated for user {$user->username}");
        }

        $this->updateNameChangePermission($user, $data);
    }

    /**
     * Non-credit currencies whose form value differs from the stored balance.
     *
     * @param  array<string, mixed>  $data
     *
     * @return list<array{CurrencyTypes, int, int}>
     */
    private function changedCurrencies(User $user, array $data): array
    {
        $currencies = app(CurrencyRepository::class);
        $changes = [];

        foreach (CurrencyTypes::cases() as $type) {
            if ($type === CurrencyTypes::Credits) {
                continue;
            }

            $current = $currencies->balance($user, $type);
            $updated = (int) ($data["currency_{$type->value}"] ?? $current);

            if ($updated !== $current) {
                $changes[] = [$type, $current, $updated];
            }
        }

        return $changes;
    }

    /** @param array<string, mixed> $data */
    private function checkUsernameChangedPermission(User $user, array $data, Rcon $rcon): void
    {
        if (! Emulator::supports(Feature::NameChangePermission)) {
            return;
        }

        if ($user->settings === null || $data['allow_change_username'] == $user->settings->can_change_name) {
            return;
        }

        if (! $rcon->isConnected()) {
            Notification::make()
                ->danger()
                ->title(__('RCON is not enabled!'))
                ->body(__('You cannot edit users because RCON is not enabled and the user is online.'))
                ->send();

            $this->halt();
        }

        $rcon->disconnectUser($user);
        $this->updateNameChangePermission($user, $data);
    }

    /** @param array<string, mixed> $data */
    private function updateNameChangePermission(User $user, array $data): void
    {
        if (! Emulator::supports(Feature::NameChangePermission) || $user->settings === null) {
            return;
        }

        $user->settings->update(['can_change_name' => ($data['allow_change_username'] ?? false) ? '1' : '0']);
    }

    /** @param array<string, mixed> $data */
    private function treatChangedCurrencies(User $user, array $data): void
    {
        foreach ($this->changedCurrencies($user, $data) as [$type, $current, $updated]) {
            app(SendCurrency::class)->execute($user, $type, $updated - $current);
        }
    }

    /** @param array<string, mixed> $data */
    private function treatChangedUserRank(User $user, array $data, Rcon $rcon): void
    {
        if ($data['rank'] == $user->rank) {
            return;
        }

        $actor = auth()->user();

        if (! $actor instanceof User || (int) $data['rank'] >= $actor->rank) {
            return;
        }

        if ($user->online && ! $rcon->isConnected()) {
            Notification::make()
                ->danger()
                ->title(__('RCON is not enabled!'))
                ->body(__('You cannot edit users because RCON is not enabled and the user is online.'))
                ->send();

            $this->halt();
        }

        if (! $user->online) {
            $user->forceFill(['rank' => $data['rank']])->save();

            return;
        }

        $rcon->alertUser($user, __('You have been disconnected because your rank has been changed. Please re-enter the hotel.'));
        $rcon->disconnectUser($user);
        $rcon->setRank($user, $data['rank']);
    }

    /** @param array<string, mixed> $data */
    private function treatChangedUserMotto(User $user, array $data, Rcon $rcon): void
    {
        if ($data['motto'] == $user->motto) {
            return;
        }

        if ($user->online && ! $rcon->isConnected()) {
            Notification::make()
                ->danger()
                ->title(__('RCON is not enabled!'))
                ->body(__('You cannot edit users because RCON is not enabled and the user is online.'))
                ->send();

            $this->halt();
        }

        if (! $user->online) {
            $user->update(['motto' => $data['motto']]);

            return;
        }

        $rcon->setMotto($user, $data['motto']);
        $rcon->alertUser($user, __('Your motto has been changed by a staff member.'));
    }

    private function userRecord(Model $record): User
    {
        if (! $record instanceof User) {
            throw new LogicException('The user editor received an unsupported model.');
        }

        return $record;
    }
}
