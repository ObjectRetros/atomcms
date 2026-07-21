<?php

namespace App\Filament\Traits;

use BackedEnum;
use Str;
use UnitEnum;

trait TranslatableResource
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

        return __(
            sprintf('filament::resources.navigations.%s', $navigationGroup),
        );
    }

    public static function getPluralModelLabel(): string
    {
        return __(sprintf(
            Str::endsWith(static::class, 'RelationManager')
                ? 'filament::resources.resources.%s.navigation_label'
                : 'filament::resources.resources.%s.plural',
            static::$translateIdentifier,
        ));
    }

    public static function getNavigationLabel(): string
    {
        return __(
            sprintf('filament::resources.resources.%s.navigation_label', static::$translateIdentifier),
        );
    }

    public static function getModelLabel(): string
    {
        return __(
            sprintf('filament::resources.resources.%s.label', static::$translateIdentifier),
        );
    }
}
