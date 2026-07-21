<?php

namespace App\Http\Middleware;

use App\Services\SettingsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ConfigureRuntimeFilesystems
{
    public function __construct(private readonly SettingsService $settings) {}

    public function handle(Request $request, Closure $next): Response
    {
        Config::set(
            'filesystems.disks.badges.root',
            $this->settings->getOrDefault('badge_path_filesystem', '/var/www/gamedata/c_images/album1584'),
        );
        Config::set(
            'filesystems.disks.ads.root',
            $this->settings->getOrDefault('ads_path_filesystem', '/var/www/gamedata/custom'),
        );

        Storage::forgetDisk(['badges', 'ads']);

        return $next($request);
    }
}
