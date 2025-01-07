<?php

namespace App\Filament\Resources\Hotel;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CustomQueryBuilder extends Builder
{
    public function __construct()
    {
        // Call the parent constructor with a dummy query
        parent::__construct(app('db')->query());
    }

    public function get($columns = ['*']): Collection
    {
        return collect(); // Return an empty collection
    }
}