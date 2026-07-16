<?php

namespace App\Services\Parsers;

use App\Services\Badge\FlashExternalTexts;
use App\Services\Badge\NitroExternalTexts;
use App\Services\SettingsService;
use App\Support\BadgeCode;
use Illuminate\Support\Facades\Storage;

/**
 * Reads and writes a badge's name/description across the client text files -
 * the Nitro external texts JSON and the flash external texts key=value file -
 * and resolves where its image lives. Backs the housekeeping badge page.
 */
class ExternalTextsParser
{
    public function __construct(
        private readonly NitroExternalTexts $nitroTexts,
        private readonly FlashExternalTexts $flashTexts,
        private readonly SettingsService $settings,
    ) {}

    /**
     * @return array{
     *     image: string|null,
     *     nitro: array{title: string, description: string}|null,
     *     flash: array{title: string, description: string}|null,
     * }
     */
    public function getBadgeData(string $code): array
    {
        $code = BadgeCode::normalize($code);

        return [
            'image' => Storage::disk('badges')->exists(BadgeCode::filename($code)) ? $this->getBadgeImageUrl($code) : null,
            'nitro' => $this->nitroTexts->find($code),
            'flash' => $this->flashTexts->find($code),
        ];
    }

    /**
     * The parameter names double as named arguments: the badge page spreads
     * its form state (title/description) into these calls.
     */
    public function updateNitroBadgeTexts(string $code, string $title = '', string $description = ''): void
    {
        $this->nitroTexts->add(BadgeCode::normalize($code), $title, $description);
    }

    public function updateFlashBadgeTexts(string $code, string $title = '', string $description = ''): void
    {
        $this->flashTexts->add(BadgeCode::normalize($code), $title, $description);
    }

    public function getBadgeImageUrl(string $code): string
    {
        $baseUrl = rtrim((string) $this->settings->getOrDefault('badges_path', '/badges'), '/');
        $path = $baseUrl . '/' . rawurlencode(BadgeCode::normalize($code)) . '.gif';

        return str_starts_with($path, 'https://') || str_starts_with($path, 'http://')
            ? $path
            : url($path);
    }
}
