<?php

namespace App\Models\Game\Furniture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemBase extends Model
{
    protected $table = 'items_base';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function icon(): string
    {
        return sprintf('%s/%s_icon.png', setting('furniture_icons_path'), $this->item_name);
    }

    public function catalogItems(): HasMany
    {
        return $this->hasMany(CatalogItem::class, 'item_ids', 'id');
    }
}
