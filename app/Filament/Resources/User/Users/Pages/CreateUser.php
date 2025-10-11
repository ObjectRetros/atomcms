<?php

namespace App\Filament\Resources\User\Users\Pages;

use App\Filament\Resources\User\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
