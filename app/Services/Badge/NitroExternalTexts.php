<?php

namespace App\Services\Badge;

use App\Services\Files\AtomicFileWriter;
use App\Services\SettingsService;
use App\Support\BadgeCode;
use JsonException;
use RuntimeException;

/**
 * Maintains the badge name/description entries in the Nitro external texts
 * JSON file, so drawn badges show their metadata in the client.
 */
class NitroExternalTexts
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
        $badgeCode = BadgeCode::normalize($badgeCode);
        $texts = $this->read();

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

    public function add(string $badgeCode, ?string $name, ?string $description): void
    {
        $badgeCode = BadgeCode::normalize($badgeCode);
        $this->rewrite(fn (array $texts): array => array_merge($texts, [
            "badge_name_{$badgeCode}" => $name,
            "badge_desc_{$badgeCode}" => $description,
        ]));
    }

    public function remove(string $badgeCode): void
    {
        $badgeCode = BadgeCode::normalize($badgeCode);
        $this->rewrite(function (array $texts) use ($badgeCode): array {
            unset($texts["badge_name_{$badgeCode}"], $texts["badge_desc_{$badgeCode}"]);

            return $texts;
        }, required: false);
    }

    /**
     * @param  callable(array<string, mixed>): array<string, mixed>  $mutate
     */
    private function rewrite(callable $mutate, bool $required = true): void
    {
        $path = $this->path();

        if (! $path || ! file_exists($path) || ! is_writable($path)) {
            if ($required) {
                throw new RuntimeException('The Nitro external texts file is not writable.');
            }

            return;
        }

        $this->files->update($path, function (string $contents) use ($mutate): string {
            try {
                $texts = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                throw new RuntimeException('The Nitro external texts file contains invalid JSON.', previous: $exception);
            }

            if (! is_array($texts)) {
                throw new RuntimeException('The Nitro external texts file must contain a JSON object.');
            }

            return json_encode($mutate($texts), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function read(): array
    {
        $path = $this->path();

        if (! $path || ! file_exists($path)) {
            return [];
        }

        $texts = json_decode((string) file_get_contents($path), true);

        return is_array($texts) ? $texts : [];
    }

    private function path(): ?string
    {
        $path = $this->settings->getOrDefault('nitro_external_texts_file');

        return is_string($path) && $path !== '' ? $path : null;
    }
}
