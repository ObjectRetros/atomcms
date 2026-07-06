<?php

namespace App\Services\Badge;

use App\Services\SettingsService;

/**
 * Maintains the badge name/description entries in the Nitro external texts
 * JSON file, so drawn badges show their metadata in the client.
 */
class NitroExternalTexts
{
    public function __construct(private readonly SettingsService $settings) {}

    public function add(string $badgeCode, ?string $name, ?string $description): void
    {
        $this->rewrite(fn (array $texts): array => array_merge($texts, [
            "badge_name_{$badgeCode}" => $name,
            "badge_desc_{$badgeCode}" => $description,
        ]));
    }

    public function remove(string $badgeCode): void
    {
        $this->rewrite(function (array $texts) use ($badgeCode): array {
            unset($texts["badge_name_{$badgeCode}"], $texts["badge_desc_{$badgeCode}"]);

            return $texts;
        });
    }

    /**
     * @param  callable(array<string, mixed>): array<string, mixed>  $mutate
     */
    private function rewrite(callable $mutate): void
    {
        $path = $this->settings->getOrDefault('nitro_external_texts_file');

        if (! $path || ! file_exists($path) || ! is_writable($path)) {
            return;
        }

        $texts = json_decode((string) file_get_contents($path), true);

        if (! is_array($texts)) {
            return;
        }

        file_put_contents($path, json_encode($mutate($texts), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
