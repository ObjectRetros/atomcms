<?php

namespace App\Services\Badge;

use App\Services\SettingsService;
use App\Support\AtomicFileWriter;
use App\Support\BadgeCode;
use InvalidArgumentException;
use RuntimeException;

/**
 * Maintains the badge name/description entries in the flash client's
 * external_flash_texts file - a key=value line format - whose path is the
 * flash_external_texts_file website setting.
 */
class FlashExternalTexts
{
    public function __construct(
        private readonly SettingsService $settings,
        private readonly AtomicFileWriter $files,
    ) {}

    /**
     * @return array{title: string, description: string}|null null when the badge has no entries
     */
    public function find(string $badgeCode): ?array
    {
        $badgeCode = BadgeCode::ensure($badgeCode);
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
        $badgeCode = BadgeCode::ensure($badgeCode);
        $path = $this->path();

        if ($path === null) {
            return;
        }

        $entries = [
            "badge_name_{$badgeCode}" => $this->value($title),
            "badge_desc_{$badgeCode}" => $this->value($description),
        ];

        $this->files->rewrite($path, function (string $contents) use ($entries): string {
            $lines = preg_split('/\r\n|\n/', rtrim($contents));
            $lines = $lines === false ? [] : $lines;
            $pending = $entries;

            foreach ($lines as $index => $line) {
                [$key] = explode('=', $line, 2);

                if (array_key_exists($key, $pending)) {
                    $lines[$index] = $key . '=' . $pending[$key];
                    unset($pending[$key]);
                }
            }

            foreach ($pending as $key => $value) {
                $lines[] = $key . '=' . $value;
            }

            return implode("\n", $lines) . "\n";
        });
    }

    /**
     * @return array<string, string>
     */
    private function all(): array
    {
        $path = $this->path();

        if ($path === null) {
            return [];
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read Flash external texts: {$path}");
        }

        $texts = [];

        foreach (preg_split('/\r\n|\n/', $contents) ?: [] as $line) {
            if (! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $texts[$key] = $value;
        }

        return $texts;
    }

    private function value(?string $value): string
    {
        $value ??= '';

        if (str_contains($value, "\r") || str_contains($value, "\n")) {
            throw new InvalidArgumentException('Flash external text values cannot contain line breaks.');
        }

        return $value;
    }

    private function path(): ?string
    {
        $path = $this->settings->getOrDefault('flash_external_texts_file');

        return is_string($path) && $path !== '' ? $path : null;
    }
}
