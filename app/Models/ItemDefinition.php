<?php

namespace App\Models;

use App\Models\User\UserItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemDefinition extends Model
{
    use HasFactory;

    protected $table = 'items_base';

    public function userItems(): HasMany
    {
        return $this->hasMany(UserItem::class, 'item_id');
    }
}
