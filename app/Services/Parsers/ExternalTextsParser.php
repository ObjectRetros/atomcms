<?php

namespace App\Services\Parsers;

use App\Services\Badge\FlashExternalTexts;
use App\Services\Badge\NitroExternalTexts;

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
        return [
            'image' => file_exists($this->badgeImagePath($code)) ? $this->getBadgeImageUrl($code) : null,
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
        $this->nitroTexts->add($code, $title, $description);
    }

    public function updateFlashBadgeTexts(string $code, string $title = '', string $description = ''): void
    {
        $this->flashTexts->add($code, $title, $description);
    }

    public function getBadgeImageUrl(string $code): string
    {
        return url(sprintf('%s/c_images/album1584/%s.gif', $this->clientPath(), $code));
    }

    private function badgeImagePath(string $code): string
    {
        return public_path(sprintf('%s/c_images/album1584/%s.gif', $this->clientPath(), $code));
    }

    private function clientPath(): string
    {
        return trim((string) config('hotel.client.flash.relative_files_path'), '/');
    }
}
