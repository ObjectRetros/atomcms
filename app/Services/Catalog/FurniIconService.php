<?php

namespace App\Services\Catalog;

use App\Models\Miscellaneous\WebsiteSetting;

/** Per-request instance cache of the website_settings base paths. */
class FurniIconService
{
    private ?string $furniBase = null;

    private ?string $catalogBase = null;

    public function furniIcon(string $catalogName): string
    {
        $base = $this->furniBase ??= $this->setting('furniture_icons_path', '/images/furniture');
        $safe = str_replace('*', '_', $catalogName);

        return $this->absolutise($base . '/' . $safe . '_icon.png');
    }

    public function pageIcon(int $iconImage): string
    {
        $base = $this->catalogBase ??= $this->setting('catalog_icons_path', '/gamedata/c_images/catalogue');
        $id = max(1, $iconImage);

        return $this->absolutise($base . '/icon_' . $id . '.png');
    }

    private function setting(string $key, string $default): string
    {
        $value = WebsiteSetting::query()->where('key', $key)->value('value');

        return rtrim($value ?: $default, '/');
    }

    private function absolutise(string $path): string
    {
        return preg_match('#^(https?:)?//#', $path) ? $path : asset($path);
    }
}
