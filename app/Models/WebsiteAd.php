<?php

namespace App\Models;

use App\Services\SettingsService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $image_url
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteAd newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteAd newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteAd query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteAd whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteAd whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteAd whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteAd whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsiteAd extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $fillable = [
        'image',
    ];

    /** @return Attribute<string, never> */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $adsPicturePath = (string) Cache::remember(
                    'ads_picture_path',
                    3600,
                    fn (): mixed => app(SettingsService::class)->getOrDefault('ads_picture_path', ''),
                );

                if (! str_starts_with($adsPicturePath, 'http')) {
                    $adsPicturePath = rtrim(config('app.url'), '/') . '/' . ltrim($adsPicturePath, '/');
                }

                return rtrim($adsPicturePath, '/') . '/' . $this->image;
            },
        );
    }
}
