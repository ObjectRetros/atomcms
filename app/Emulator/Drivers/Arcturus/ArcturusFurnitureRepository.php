<?php

namespace App\Emulator\Drivers\Arcturus;

use App\Emulator\Contracts\FurnitureRepository;
use App\Models\User;

/**
 * Arcturus inventory rows live in items, referencing items_base via item_id.
 */
class ArcturusFurnitureRepository implements FurnitureRepository
{
    public function grant(User $user, int $baseItemId, int $amount): void
    {
        for ($i = 0; $i < $amount; $i++) {
            $user->items()->create(['item_id' => $baseItemId]);
        }
    }
}
