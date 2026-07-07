<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class InstallationService
{
    private ?bool $installationComplete = null;

    public function isComplete(): bool
    {
        if ($this->installationComplete !== null) {
            return $this->installationComplete;
        }

        if (Cache::has('app_installed')) {
            $this->installationComplete = true;

            return true;
        }

        try {
            if (! Schema::hasTable('website_installation')) {
                $this->installationComplete = false;

                return false;
            }

            // Any completed row counts: the first-visit race can leave
            // duplicate rows, and completion must not hinge on row order.
            $isComplete = DB::table('website_installation')->where('completed', true)->exists();

            if ($isComplete) {
                Cache::rememberForever('app_installed', fn () => true);
            }

            $this->installationComplete = $isComplete;

            return $isComplete;
        } catch (Throwable) {
            $this->installationComplete = false;

            return false;
        }
    }

    public static function setComplete(): void
    {
        Cache::rememberForever('app_installed', fn () => true);
    }

    public static function clearCache(): void
    {
        Cache::forget('app_installed');
    }
}
