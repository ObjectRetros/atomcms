<?php

namespace App\Filament\Resources\User\UserResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Enums\NotificationType;
use App\Models\User\UserNotification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\User\UserResource;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make(__('filament::resources.actions.send_notifications'))
                ->modal()
                ->color('gray')
                ->modalHeading(__('filament::resources.actions.send_notifications'))
                ->icon('heroicon-o-bell')
                ->form([
                    Select::make('users')
                        ->label(__('filament::resources.inputs.users'))
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search): array => User::where('username', 'like', "%{$search}%")->limit(50)->pluck('username', 'id')->toArray())
                        ->multiple()
                        ->native(false)
                        ->nullable(),

                    TextInput::make('message')
                        ->label(__('filament::resources.inputs.message'))
                        ->maxLength(100)
                        ->required(),

                    TextInput::make('url')
                        ->label(__('filament::resources.inputs.url'))
                        ->nullable(),

                    Toggle::make('as_staff')
                        ->label(__('filament::resources.inputs.as_staff'))
                        ->default(false),
                ])
                ->action(function (array $data) {
                    $notifications = collect();
                    $allUsersId = collect($data['users'])->values();
                    $senderId = $data['as_staff'] ? null : auth()->id();

                    if($allUsersId->isEmpty()) {
                        $allUsersId = User::select('id')->get()->pluck('id');
                    }

                    $allUsersId->each(function ($userId) use ($senderId, $data, $notifications) {
                        $notifications->push([
                            'sender_id' => $senderId,
                            'recipient_id' => $userId,
                            'type' => NotificationType::HousekeepingCustomMessage,
                            'message' => $data['message'],
                            'url' => $data['url'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    });

                    UserNotification::insert($notifications->toArray());

                    Notification::make()
                        ->body(__('Notification sent successfully.'))
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->send();
                }),

            Actions\CreateAction::make(),
        ];
    }
}
