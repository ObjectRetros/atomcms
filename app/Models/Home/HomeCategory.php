<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $order
 * @property string $name
 * @property string|null $icon
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, HomeItem> $homeItems
 * @property-read int|null $home_items_count
 */
class HomeCategory extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $guarded = [];

    /** @return HasMany<HomeItem, $this> */
    public function homeItems(): HasMany
    {
        return $this->hasMany(HomeItem::class);
    }
}
