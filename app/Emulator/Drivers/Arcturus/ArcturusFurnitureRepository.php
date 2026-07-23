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
        if ($amount < 1) {
            return;
        }

        // One multi-row insert instead of a query per item; the remaining
        // columns fall back to the table defaults.
        $rows = array_fill(0, $amount, [
            'user_id' => $user->id,
            'item_id' => $baseItemId,
        ]);

        $user->items()->insert($rows);
    }
}
