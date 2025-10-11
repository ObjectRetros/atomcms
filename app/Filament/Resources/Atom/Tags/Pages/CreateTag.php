<?php

namespace App\Filament\Resources\Atom\Tags\Pages;

use App\Filament\Resources\Atom\Tags\TagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;
}
