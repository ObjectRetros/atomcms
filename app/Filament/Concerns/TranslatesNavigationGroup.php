<?php

namespace App\Filament\Concerns;

use BackedEnum;
use UnitEnum;

/**
 * Translates a resource's navigation group.
 *
 * Filament groups the sidebar by the rendered group name, so a resource that
 * skips this lands in a second group with the same English name next to the
 * translated one. Driver-specific resources need this even when they have no
 * translated labels of their own.
 */
trait TranslatesNavigationGroup
{
    public static function getNavigationGroup(): ?string
    {
        $navigationGroup = static::$navigationGroup;

        if ($navigationGroup === null) {
            return null;
        }

        if ($navigationGroup instanceof BackedEnum) {
            $navigationGroup = (string) $navigationGroup->value;
        } elseif ($navigationGroup instanceof UnitEnum) {
            $navigationGroup = $navigationGroup->name;
        }

        return __(sprintf('filament::resources.navigations.%s', $navigationGroup));
    }
}
