<?php

namespace App\Services\Badge;

use App\Services\SettingsService;

/**
 * Maintains the badge name/description entries in the flash client's
 * external_flash_texts file - a key=value line format - whose path is the
 * flash_external_texts_file website setting.
 */
class FlashExternalTexts
{
    public function __construct(private readonly SettingsService $settings) {}

    /**
     * @return array{title: string, description: string}|null null when the badge has no entries
     */
    public function find(string $badgeCode): ?array
    {
        $texts = $this->all();

        $name = $texts["badge_name_{$badgeCode}"] ?? null;
        $description = $texts["badge_desc_{$badgeCode}"] ?? null;

        if ($name === null && $description === null) {
            return null;
        }

        return [
            'title' => (string) ($name ?? ''),
            'description' => (string) ($description ?? ''),
        ];
    }

    /**
     * Insert or update the badge entries, leaving every other line untouched.
     */
    public function add(string $badgeCode, ?string $title, ?string $description): void
    {
        $path = $this->path();

        if (! $path || ! file_exists($path) || ! is_writable($path)) {
            return;
        }

        $lines = preg_split('/\r\n|\n/', rtrim((string) file_get_contents($path)));
        $lines = $lines === false ? [] : $lines;

        $entries = [
            "badge_name_{$badgeCode}" => (string) ($title ?? ''),
            "badge_desc_{$badgeCode}" => (string) ($description ?? ''),
        ];

        foreach ($lines as $index => $line) {
            [$key] = explode('=', $line, 2);

            if (array_key_exists($key, $entries)) {
                $lines[$index] = $key . '=' . $entries[$key];
                unset($entries[$key]);
            }
        }

        foreach ($entries as $key => $value) {
            $lines[] = $key . '=' . $value;
        }

        file_put_contents($path, implode("\n", $lines) . "\n");
    }

    /**
     * @return array<string, string>
     */
    private function all(): array
    {
        $path = $this->path();

        if (! $path || ! file_exists($path)) {
            return [];
        }

        $texts = [];

        foreach (preg_split('/\r\n|\n/', (string) file_get_contents($path)) ?: [] as $line) {
            if (! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $texts[$key] = $value;
        }

        return $texts;
    }

    private function path(): ?string
    {
        $path = $this->settings->getOrDefault('flash_external_texts_file');

        return is_string($path) && $path !== '' ? $path : null;
    }
}
