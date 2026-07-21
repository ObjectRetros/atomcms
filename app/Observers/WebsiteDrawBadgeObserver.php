<?php

namespace App\Observers;

use App\Emulator\Contracts\BadgeRepository;
use App\Models\User;
use App\Models\WebsiteDrawBadge;
use App\Services\Badge\NitroExternalTexts;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WebsiteDrawBadgeObserver
{
    public function __construct(
        private readonly BadgeRepository $badges,
        private readonly NitroExternalTexts $externalTexts,
    ) {}

    public function updated(WebsiteDrawBadge $websiteDrawBadge): void
    {
        if (! $websiteDrawBadge->wasChanged() || ! $websiteDrawBadge->badge_path) {
            return;
        }

        $badgeCode = pathinfo($websiteDrawBadge->badge_path, PATHINFO_FILENAME);
        $owner = User::find($websiteDrawBadge->user_id);

        if (! $websiteDrawBadge->published) {
            if ($owner !== null) {
                $this->badges->revoke($owner, $badgeCode);
            }

            $this->externalTexts->remove($badgeCode);

            return;
        }

        if ($owner !== null) {
            $this->badges->grant($owner, $badgeCode);
        }

        try {
            $this->externalTexts->add($badgeCode, $websiteDrawBadge->badge_name, $websiteDrawBadge->badge_desc);
        } catch (RuntimeException $exception) {
            Log::warning('Failed to update Nitro external texts for a drawn badge.', [
                'badge_id' => $websiteDrawBadge->getKey(),
                'exception_class' => $exception::class,
            ]);
        }
    }
}
