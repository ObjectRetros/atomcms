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

            $installation = DB::table('website_installation')->first();

            $isComplete = $installation && $installation->completed;

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
