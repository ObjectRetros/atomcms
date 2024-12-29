<?php

namespace App\Filament\Resources\User\UserResource\Pages;

use App\Models\Game\Player\UserCurrency;
use Filament\Actions;
use App\Services\RconService;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\User\UserResource;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return static::$resource::fillWithOutsideData(
            $this->getRecord(),
            $data
        );
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()->with(['currencies', 'settings']);
    }

    protected function beforeSave(): void
    {
        $user = $this->getRecord();
        $data = $this->form->getState();

        if ($data['rank'] >= auth()->user()->rank) {
            Notification::make()
                ->danger()
                ->title(__('You cannot edit this user!'))
                ->body(__('You cannot edit users with a higher rank than yours.'))
                ->send();

            $this->halt();
            return;
        }

        $rconEnabled = config('hotel.rcon.enabled');
        $rcon = app(RconService::class);

        if (!$user->online) {
            $this->treatChangedCurrenciesWithoutRcon($user, $data);
            return;
        }

        if ($user->online && !$rconEnabled) {
            Notification::make()
                ->danger()
                ->title(__('RCON is not enabled!'))
                ->body(__('You cannot edit users because RCON is not enabled and the user is online.'))
                ->send();

            $this->halt();
            return;
        }

        if ($data['credits'] != $user->credits) {
            $rcon->sendSafelyFromDashboard('giveCurrency',
                [$user, 'credits', -$user->credits + $data['credits']],
                'RCON: Failed to send credits'
            );
        }

        $this->checkUsernameChangedPermission($user, $data, $rcon);
        $this->treatChangedCurrencies($user, $data, $rcon);
        $this->treatChangedUserRank($user, $data, $rcon);
        $this->treatChangedUserMotto($user, $data, $rcon);
    }

    private function treatChangedCurrenciesWithoutRcon(Model $user, array $data): void
    {
        if ($data['credits'] != $user->credits) {
            $user->credits = $data['credits'];
        }

        $user->currencies->each(function (UserCurrency $currency) use ($data, $user) {
            $updatedCurrencyAmount = collect($data)
                ->get("currency_{$currency->type}", $currency->amount);

            if ($updatedCurrencyAmount == $currency->amount) return;

            $user->currencies()->whereType($currency->type)->update([
                'amount' => $updatedCurrencyAmount
            ]);
        });

        $user->settings->update([
            'can_change_name' => $data['allow_change_username'] ? '1' : '0',
        ]);
    }

    private function checkUsernameChangedPermission(Model $user, array $data, RconService $rcon): void
    {
        if ($data['allow_change_username'] == $user->settings->can_change_name) return;

        $rcon->sendSafelyFromDashboard('changeUsername',
            [$user, $data['allow_change_username']],
            'RCON: Failed to set can_change_username'
        );
    }

    private function treatChangedCurrencies(Model $user, array $data, RconService $rcon): void
    {
        $user->currencies->each(function (UserCurrency $currency) use ($data, $user, $rcon) {
            $updatedCurrencyAmount = collect($data)
                ->get("currency_{$currency->type}", $currency->amount);

            if ($updatedCurrencyAmount == $currency->amount) return;

            $rcon->sendSafelyFromDashboard('giveCurrency',
                [$user, $currency->type, -$currency->amount + $updatedCurrencyAmount],
                "RCON: Failed to send a currency",
            );
        });
    }

    private function treatChangedUserRank(Model $user, array $data, RconService $rcon): void
    {
        if ($data['rank'] == $user->rank) return;
        if ($data['rank'] > auth()->user()->rank) return;

        if ($user->online) {
            $rcon->sendSafelyFromDashboard('alertUser',
                [$user, 'Your rank has been changed. Please, re-enter.'],
                "RCON: Failed to send a user alert",
            );

            sleep(2);
        }

        $rcon->sendSafelyFromDashboard('disconnectUser', [$user], "RCON: Failed to disconnect a user");
        $rcon->sendSafelyFromDashboard('setRank', [$user, $data['rank']], "RCON: Failed to update the user rank");
    }

    private function treatChangedUserMotto(Model $user, array $data, RconService $rcon): void
    {
        if ($data['motto'] == $user->motto) return;

        $rcon->sendSafelyFromDashboard('setMotto', [$user, $data['motto']], "RCON: Failed to update the user motto");
    }
}
