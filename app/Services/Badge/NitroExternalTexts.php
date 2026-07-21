<?php

namespace App\Services\Badge;

use App\Services\SettingsService;
use App\Support\AtomicFileWriter;
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
        $badgeCode = BadgeCode::ensure($badgeCode);
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
        $badgeCode = BadgeCode::ensure($badgeCode);

        $this->rewrite(fn (array $texts): array => array_merge($texts, [
            "badge_name_{$badgeCode}" => $name,
            "badge_desc_{$badgeCode}" => $description,
        ]));
    }

    public function remove(string $badgeCode): void
    {
        $badgeCode = BadgeCode::ensure($badgeCode);

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
        $path = $this->path();

        if ($path === null) {
            return;
        }

        $this->files->rewrite(
            $path,
            fn (string $contents): string => json_encode(
                $mutate($this->decode($contents, $path)),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
            ) . "\n",
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function read(): array
    {
        $path = $this->path();

        if ($path === null) {
            return [];
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read Nitro external texts: {$path}");
        }

        return $this->decode($contents, $path);
    }

    /** @return array<string, mixed> */
    private function decode(string $contents, string $path): array
    {
        try {
            $texts = json_decode($contents, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException("Nitro external texts contain invalid JSON: {$path}", previous: $exception);
        }

        if (! is_object($texts)) {
            throw new RuntimeException("Nitro external texts must contain a JSON object: {$path}");
        }

        return get_object_vars($texts);
    }

    private function path(): ?string
    {
        $path = $this->settings->getOrDefault('nitro_external_texts_file');

        return is_string($path) && $path !== '' ? $path : null;
    }
}
