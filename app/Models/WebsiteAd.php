<?php

namespace App\Models;

use App\Services\SettingsService;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class WebsiteAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
    ];

    public function getImageUrlAttribute(): string
    {
        $settingsService = app(SettingsService::class);

        $adsPicturePath = Cache::remember('ads_picture_path', 3600, function () use ($settingsService) {
            return $settingsService->getOrDefault('ads_picture_path');
        });

        if (! str_starts_with($adsPicturePath, 'http')) {
            $adsPicturePath = rtrim(config('app.url'), '/').'/'.ltrim($adsPicturePath, '/');
        }

        return rtrim($adsPicturePath, '/').'/'.$this->image;
    }

    protected static function booted()
    {
        static::deleting(function ($websiteAd) {
            try {
                $websiteAd->configureAdsDisk();

                logger()->info('Attempting to delete image file:', ['file' => $websiteAd->image]);

                if ($websiteAd->image && Storage::disk('ads')->exists($websiteAd->image)) {
                    Storage::disk('ads')->delete($websiteAd->image);
                    logger()->info('Image file deleted:', ['file' => $websiteAd->image]);
                } else {
                    logger()->warning('Image file not found:', ['file' => $websiteAd->image]);
                }
            } catch (Exception $e) {
                logger()->error('Failed to delete image file:', [
                    'file' => $websiteAd->image,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

    protected function configureAdsDisk(): void
    {
        $settingsService = app(SettingsService::class);

        $adsPath = Cache::remember('ads_path_filesystem', 3600, function () use ($settingsService) {
            return $settingsService->getOrDefault('ads_path_filesystem');
        });

        config(['filesystems.disks.ads' => [
            'driver' => 'local',
            'root' => $adsPath,
        ]]);
    }
}
