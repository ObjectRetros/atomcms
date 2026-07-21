<?php

namespace App\Filament\Resources\Hotel\CatalogEditors\Forms;

use App\Services\Catalog\FurniIconService;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Illuminate\Support\HtmlString;

class CatalogPageForm
{
    /** @return array<int, Component> */
    public static function schema(): array
    {
        $icons = app(FurniIconService::class);

        return [
            Forms\Components\TextInput::make('caption')
                ->label('Name')
                ->maxLength(128)
                ->required(),

            Forms\Components\TextInput::make('caption_save')
                ->label('Internal tag')
                ->maxLength(25)
                ->nullable()
                ->extraInputAttributes([
                    'pattern' => '[a-z]*',
                    'spellcheck' => 'false',
                    'autocomplete' => 'off',
                ])
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set) => $set('caption_save', self::sanitizeTag($state)))
                ->rules(['nullable', 'regex:/^[a-z]*$/'])
                ->validationMessages(['regex' => 'Use lowercase letters only (a–z).'])
                ->helperText('Lowercase letters only. Leave empty to omit.'),

            Forms\Components\TextInput::make('order_num')
                ->label('Order')
                ->numeric()
                ->minValue(0)
                ->step(1)
                ->required()
                ->helperText('Lower number appears earlier in the menu.'),

            Forms\Components\TextInput::make('icon_image')
                ->label('Icon number')
                ->numeric()
                ->minValue(1)
                ->required()
                ->default(1)
                ->live()
                ->helperText(function ($get) use ($icons) {
                    $id = (int) ($get('icon_image') ?: 1);
                    $url = e($icons->pageIcon($id));
                    $fallback = e($icons->pageIcon(1));

                    return new HtmlString(<<<HTML
                        <div class="mt-2 flex items-center gap-3">
                            <img src="{$url}" alt="icon {$id}" class="h-8 w-8 object-contain"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='{$fallback}'"
                                style="image-rendering: pixelated; image-rendering: crisp-edges;">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Icon #{$id}</span>
                        </div>
                    HTML);
                }),
        ];
    }

    public static function sanitizeTag(?string $value): string
    {
        $sanitized = $value ? preg_replace('/[^a-z]/', '', $value) : null;

        return is_string($sanitized) ? strtolower($sanitized) : '';
    }
}
