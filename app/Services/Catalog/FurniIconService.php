<?php

namespace App\Services\Catalog;

use App\Services\SettingsService;

/** Per-request instance cache of the website_settings base paths. */
class FurniIconService
{
    private ?string $furniBase = null;

    private ?string $catalogBase = null;

    public function __construct(private readonly SettingsService $settings) {}

    public function furniIcon(string $catalogName): string
    {
        $base = $this->furniBase ??= $this->basePath('furniture_icons_path', '/images/furniture');
        $safe = str_replace('*', '_', $catalogName);

        return $this->absolutise($base . '/' . $safe . '_icon.png');
    }

    public function pageIcon(int $iconImage): string
    {
        $base = $this->catalogBase ??= $this->basePath('catalog_icons_path', '/gamedata/c_images/catalogue');
        $id = max(1, $iconImage);

        return $this->absolutise($base . '/icon_' . $id . '.png');
    }

    private function basePath(string $key, string $default): string
    {
        $value = $this->settings->getOrDefault($key);

        return rtrim(is_string($value) && $value !== '' ? $value : $default, '/');
    }

    private function absolutise(string $path): string
    {
        return preg_match('#^(https?:)?//#', $path) ? $path : asset($path);
    }
}
