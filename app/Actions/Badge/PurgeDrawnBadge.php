<?php

namespace App\Actions\Badge;

use App\Emulator\Contracts\BadgeRepository;
use App\Models\User;
use App\Models\WebsiteDrawBadge;
use App\Services\Badge\NitroExternalTexts;
use RuntimeException;

/**
 * Removes every trace of a drawn badge before its record is deleted: the
 * owner's granted badge, the external texts entries and the image file.
 */
class PurgeDrawnBadge
{
    public function __construct(
        private readonly BadgeRepository $badges,
        private readonly NitroExternalTexts $externalTexts,
    ) {}

    public function execute(WebsiteDrawBadge $badge): void
    {
        $badgeCode = pathinfo($badge->badge_path, PATHINFO_FILENAME);

        if ($badge->published && ($owner = User::find($badge->user_id)) !== null) {
            $this->badges->revoke($owner, $badgeCode);
        }

        $this->externalTexts->remove($badgeCode);

        if ($badge->badge_path && is_file($badge->badge_path) && ! unlink($badge->badge_path)) {
            throw new RuntimeException("Unable to remove badge image: {$badge->badge_path}");
        }
    }
}
