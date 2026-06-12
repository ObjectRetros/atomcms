<?php

namespace App\Policies;

class CatalogItemPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_catalog_pages';
    }
}
