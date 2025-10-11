<?php

namespace App\Filament\Resources\User\Users\Pages;

use App\Actions\SendCurrency;
use App\Enums\CurrencyTypes;
use App\Filament\Resources\User\Users\UserResource;
use App\Models\Game\Player\UserCurrency;
use App\Services\RconService;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditUser extends EditRecord
{
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

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()->with(['currencies', 'settings']);
    }

    /**
     * @throws Halt
     */
    protected function beforeSave(): void
    {
        $user = $this->getRecord();
        $data = $this->form->getState();

        if ($data['rank'] > auth()->user()->rank) {
            Notification::make()
                ->danger()
                ->title(__('You cannot edit this user!'))
                ->body(__('You cannot edit users with a higher rank than yours.'))
                ->send();

            $this->halt();
        }

        $rcon = app(RconService::class);

        if (! $user->online) {
            DB::transaction(function () use ($user, $data) {
                $this->treatChangedCurrenciesWithoutRcon($user, $data);
            });

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

        DB::transaction(function () use ($user, $data, $rcon) {
            if ($data['credits'] != $user->credits) {
                $rcon->giveCredits($user, -$user->credits + $data['credits']);
            }

            $this->checkUsernameChangedPermission($user, $data, $rcon);
            $this->treatChangedCurrencies($user, $data, $rcon);
            $this->treatChangedUserRank($user, $data, $rcon);
            $this->treatChangedUserMotto($user, $data, $rcon);
        });
    }

    private function treatChangedCurrenciesWithoutRcon(Model $user, array $data): void
    {
        $user->currencies->each(function (UserCurrency $currency) use ($data, $user) {
            $updatedCurrencyAmount = $data["currency_{$currency->type}"] ?? $currency->amount;
            if ($updatedCurrencyAmount == $currency->amount) {
                return;
            }

            $updated = $user->currencies()->where('type', $currency->type)->update(['amount' => $updatedCurrencyAmount]);

            if ($updated) {
                activity()
                    ->performedOn($currency)
                    ->withProperties(['old_amount' => $currency->amount, 'new_amount' => $updatedCurrencyAmount, 'user_id' => $user->id, 'type' => $currency->type])
                    ->event('updated')
                    ->log("Currency updated for user {$user->username}");

            } else {
                activity()
                    ->withProperties(['user_id' => $user->id, 'type' => $currency->type])
                    ->event('failed_update')
                    ->log("Failed to update currency for user {$user->username}");
            }
        });

        $user->settings->update(['can_change_name' => $data['allow_change_username'] ? '1' : '0']);
    }

    private function checkUsernameChangedPermission(Model $user, array $data, RconService $rcon): void
    {
        if ($data['allow_change_username'] == $user->settings->can_change_name) {
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
        $user->settings->update(['can_change_name' => $data['allow_change_username'] ? '1' : '0']);
    }

    private function treatChangedCurrencies(Model $user, array $data, RconService $rcon): void
    {
        $user->currencies->each(function (UserCurrency $currency) use ($data, $user) {
            $updatedCurrencyAmount = $data["currency_{$currency->type}"] ?? $currency->amount;
            $currencyType = match ($currency->type) {
                CurrencyTypes::Duckets => 'duckets',
                CurrencyTypes::Diamonds => 'diamonds',
                CurrencyTypes::Points => 'points',
            };

            if ($updatedCurrencyAmount == $currency->amount) {
                return;
            }

            app(SendCurrency::class)->execute($user, $currencyType, -$currency->amount + $updatedCurrencyAmount);
        });
    }

    private function treatChangedUserRank(Model $user, array $data, RconService $rcon): void
    {
        if ($data['rank'] == $user->rank) {
            return;
        }
        if ($data['rank'] > auth()->user()->rank) {
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
            $user->update(['rank' => $data['rank']]);

            return;
        }

        $rcon->alertUser($user, __('You have been disconnected because your rank has been changed. Please re-enter the hotel.'));
        sleep(2);

        $rcon->disconnectUser($user);
        $rcon->setRank($user, $data['rank']);
    }

    private function treatChangedUserMotto(Model $user, array $data, RconService $rcon): void
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
}
