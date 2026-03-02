<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use App\Services\SettingsService;

class WebsiteBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge_key',
        'badge_name',
        'badge_description',
    ];
    protected static function boot()
    {
        parent::boot();

        static::created(function ($badge) {
            self::updateUITextsJson($badge);
        });

        static::updated(function ($badge) {
            self::updateUITextsJson($badge);
        });

        static::deleting(function ($badge) {
            self::removeFromUITextsJson($badge);
        });
    }

    public static function updateUITextsJson($badge)
    {
        $uiTextsPath = app(\App\Services\SettingsService::class)->getOrDefault('nitro_ui_texts_path', '');
        
        if (empty($uiTextsPath)) {
            \Log::warning("nitro_ui_texts_path non configuré en DB");
            return;
        }
        
        $fullPath = rtrim($uiTextsPath, '/') . '/UITexts.json';
        
        if (!File::exists($fullPath)) {
            \Log::warning("UITexts.json not found: " . $fullPath);
            return;
        }

        $jsonContent = File::get($fullPath);
        $data = json_decode($jsonContent, true) ?: [];

        $badgeNameKey = "badge_name_" . $badge->badge_key;
        $badgeDescKey = "badge_desc_" . $badge->badge_key;

        $data[$badgeNameKey] = $badge->badge_name;
        $data[$badgeDescKey] = $badge->badge_description;

        File::put($fullPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
        touch($fullPath);
    
        \Log::info("Badge added + cache busting", ['badge_key' => $badge->badge_key]);
    }

    public static function removeFromUITextsJson($badge)
    {
        $uiTextsPath = app(\App\Services\SettingsService::class)->getOrDefault('nitro_ui_texts', '');
        
        if (empty($uiTextsPath)) return;
        
        $fullPath = rtrim($uiTextsPath, '/') . '/UITexts.json';
        
        if (!File::exists($fullPath)) return;

        $jsonContent = File::get($fullPath);
        $data = json_decode($jsonContent, true) ?: [];

        $badgeNameKey = "badge_name_" . $badge->badge_key;
        $badgeDescKey = "badge_desc_" . $badge->badge_key;

        unset($data[$badgeNameKey], $data[$badgeDescKey]);

        File::put($fullPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
