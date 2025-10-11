<?php

namespace App\Filament\Tables\Columns;

use Filament\Tables\Columns\Column;

class UserAvatarColumn extends Column
{
    protected ?string $avatarOptions = null;

    protected ?string $figurePointer = null;

    protected string $view = 'filament.tables.columns.user-avatar';

    public function getAvatarUrl(): string
    {
        $record = $this->getRecord();
        $figureImagerUrl = setting('avatar_imager');

        if (! $figureImagerUrl) {
            return '';
        }

        $figure = ! $this->figurePointer
            ? $record->look
            : data_get($record, $this->figurePointer);

        return "{$figureImagerUrl}{$figure}{$this->avatarOptions}";
    }

    public function options(string $avatarOptions): UserAvatarColumn
    {
        $this->avatarOptions = $avatarOptions;

        return $this;
    }

    /**
     * Used to reference the user's figure string through relationships in a Laravel model. By default it will take the look property of the main class.
     */
    public function pointer(string $figurePointer): UserAvatarColumn
    {
        $this->figurePointer = $figurePointer;

        return $this;
    }
}
