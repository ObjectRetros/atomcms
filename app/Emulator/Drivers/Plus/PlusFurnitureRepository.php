<?php

namespace App\Emulator\Drivers\Plus;

use App\Emulator\Contracts\FurnitureRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Plus EMU inventory rows live in items, referencing furniture via base_item.
 */
class PlusFurnitureRepository implements FurnitureRepository
{
    public function grant(User $user, int $baseItemId, int $amount): void
    {
        $rows = array_fill(0, $amount, [
            'user_id' => $user->id,
            'base_item' => $baseItemId,
            'room_id' => 0,
            'extra_data' => '',
        ]);

        DB::table('items')->insert($rows);
    }
}
