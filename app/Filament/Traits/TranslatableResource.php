<?php

namespace App\Filament\Traits;

use Str;

trait TranslatableResource
{
    public static function getNavigationGroup(): ?string
    {
        return __(
            sprintf('filament::resources.navigations.%s', static::$navigationGroup),
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
