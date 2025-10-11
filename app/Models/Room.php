<?php

namespace App\Models;

use App\Models\User\UserItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(UserItem::class);
    }

    public function replicateForUser(User $user): self
    {
        $replicatedRoom = $this->replicate();

        $replicatedRoom->owner_id = $user->id;
        $replicatedRoom->owner_name = $user->username;
        $replicatedRoom->score = 0;
        $replicatedRoom->guild_id = 0;
        $replicatedRoom->is_public = '0';
        $replicatedRoom->is_staff_picked = '0';

        $replicatedRoom->save();

        $items = [];

        foreach ($this->items as $item) {
            $replicatedItem = $item->replicate();

            $replicatedItem->user_id = $user->id;
            $replicatedItem->room_id = $replicatedRoom->id;

            $items[] = $replicatedItem;
        }

        $replicatedRoom->items()->saveMany($items);

        return $replicatedRoom;
    }
}
