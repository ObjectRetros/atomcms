<?php

namespace App\Emulator\Contracts;

use App\Models\User;

/**
 * Writes furniture into a player's inventory on the emulator database - the
 * offline path used when RCON is down. Arcturus items reference their base
 * item through item_id, Plus through base_item.
 */
interface FurnitureRepository
{
    /**
     * Place copies of a base furniture item into the user's inventory.
     */
    public function grant(User $user, int $baseItemId, int $amount): void;
}
