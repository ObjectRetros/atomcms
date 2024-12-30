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

        $rconService = app(RconService::class);

        if (!$user->online) {
            $this->updateOfflineUserWithoutRcon($user, $data);
            return;
        }

        if ($user->online && !$rconService->isConnected()) {
            Notification::make()
                ->danger()
                ->title(__('RCON is not connected!'))
                ->body(__('You cannot edit users because RCON is not connected and the user is online.'))
                ->send();

            $this->halt();
            return;
        }

        $this->updateUserMotto($user, $data, $rconService);
        $this->updateUserCredits($user, $data, $rconService);
        $this->updateUserCurrencies($user, $data, $rconService);
        $this->checkUsernameChangePermission($user, $data, $rconService);
        $this->updateUserRank($user, $data, $rconService);
    }

    private function updateOfflineUserWithoutRcon(Model $user, array $data): void
    {
        if ($data['motto'] !== $user->motto) {
            $user->update([
                'motto' => $data['motto'] != '' ? $data['motto'] : '',
            ]);

            Notification::make()
                ->success()
                ->title(__('Success!'))
                ->body(__('Successfully updated user motto.'))
                ->send();
        }

        if ($data['credits'] != $user->credits) {
            $total = (0 - $user->credits) + $data['credits'];

            $user->update([
                'credits' => $user->credits + $total,
            ]);

            Notification::make()
                ->success()
                ->title(__('Success!'))
                ->body(__('Successfully updated user credits.'))
                ->send();
        }

        $user->currencies->each(function (UserCurrency $currency) use ($data, $user) {
            $updatedCurrencyAmount = collect($data)
                ->get("currency_{$currency->type}", $currency->amount);

            if ($updatedCurrencyAmount == $currency->amount) return;

            $user->currencies()->whereType($currency->type)->update([
                'amount' => $updatedCurrencyAmount
            ]);

            Notification::make()
                ->success()
                ->title(__('Success!'))
                ->body(__('Successfully updated user currencies (ID: '.$currency->type.').'))
                ->send();
        });

        if ($data['allow_change_username'] != $user->settings->can_change_name) {
            $user->settings->update([
                'can_change_name' => $data['allow_change_username'] ? '1' : '0',
            ]);

            Notification::make()
                ->success()
                ->title(__('Success!'))
                ->body(__('Successfully updated user change name permission.'))
                ->send();
        }

        if ($data['rank'] !== $user->rank) {
            $user->update([
                'rank' => $data['rank'],
            ]);

            Notification::make()
                ->success()
                ->title(__('Success!'))
                ->body(__('Successfully updated user rank.'))
                ->send();
        }
    }

    private function updateUserMotto(Model $user, array $data, RconService $rconService): void
    {
        if($data['motto'] == $user->motto) return;

        $rconService->setMotto($user, $data['motto'] != '' ? $data['motto'] : '');

        Notification::make()
            ->success()
            ->title(__('Success!'))
            ->body(__('Successfully updated user motto.'))
            ->send();
    }

    private function updateUserCredits(Model $user, array $data, RconService $rconService): void
    {
        if($data['credits'] == $user->credits) return;

        $rconService->giveCredits($user, -$user->credits + $data['credits']);

        Notification::make()
            ->success()
            ->title(__('Success!'))
            ->body(__('Successfully updated user credits.'))
            ->send();
    }

    private function updateUserCurrencies(Model $user, array $data, RconService $rconService): void
    {
        if ($data['credits'] != $user->credits) {
            $rconService->giveCredits($user, -$user->credits + $data['credits']);

            Notification::make()
                ->success()
                ->title(__('Success!'))
                ->body(__('Successfully updated user credits.'))
                ->send();
        }

        $user->currencies->each(function (UserCurrency $currency) use ($data, $user, $rconService) {
            $updatedCurrencyAmount = collect($data)
                ->get("currency_{$currency->type}", $currency->amount);

            if ($updatedCurrencyAmount == $currency->amount) return;

            $rconService->givePointsByID($user, $currency->type, -$currency->amount + $updatedCurrencyAmount);

            Notification::make()
                ->success()
                ->title(__('Success!'))
                ->body(__('Successfully updated user currencies (ID: '.$currency->type.').'))
                ->send();
        });
    }

    private function checkUsernameChangePermission(Model $user, array $data, RconService $rconService): void
    {
        if ($data['allow_change_username'] == $user->settings->can_change_name) return;

        $rconService->changeUsername($user, $data['allow_change_username']);

        Notification::make()
            ->success()
            ->title(__('Success!'))
            ->body(__('Successfully updated user change name permission.'))
            ->send();
    }

    private function updateUserRank(Model $user, array $data, RconService $rconService): void
    {
        if($data['rank'] == $user->rank) return;
        if($data['rank'] > auth()->user()->rank) return;

        $rconService->setRank($user, $data['rank']);

        Notification::make()
            ->success()
            ->title(__('Success!'))
            ->body(__('Successfully updated user rank.'))
            ->send();
    }
}
