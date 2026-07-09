<?php

namespace App\Policies;

class CatalogPagePolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_catalog_pages';
    }

    protected function deletePermission(): string
    {
        return 'delete_catalog_pages';
    }
}
