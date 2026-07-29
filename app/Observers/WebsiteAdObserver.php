<?php

namespace App\Observers;

use App\Models\WebsiteAd;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Storage;
use Throwable;

class WebsiteAdObserver
{
    /**
     * Removes the ad's image file once the record is gone. Runs on the
     * "deleted" model event, so it also fires for each record removed by a
     * bulk action that deletes models individually.
     */
    public function deleted(WebsiteAd $websiteAd): void
    {
        if (! $websiteAd->image) {
            return;
        }

        try {
            $disk = Storage::build([
                'driver' => 'local',
                'root' => (string) app(SettingsService::class)->getOrDefault(
                    'ads_path_filesystem',
                    config('filesystems.disks.ads.root'),
                ),
            ]);

            if ($disk->exists($websiteAd->image)) {
                $disk->delete($websiteAd->image);
            } else {
                logger()->warning('Ad image file not found while deleting ad', ['file' => $websiteAd->image]);
            }
        } catch (Throwable $exception) {
            logger()->error('Failed to delete ad image file', [
                'file' => $websiteAd->image,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
