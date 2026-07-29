<?php

namespace App\Emulator\Drivers\Arcturus;

use App\Emulator\Contracts\RoomRepository;
use App\Emulator\Data\RoomSummary;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Collection;

class ArcturusRoomRepository implements RoomRepository
{
    public function forHome(User $user): Collection
    {
        return Room::query()
            ->where('owner_id', $user->id)
            ->get(['id', 'name', 'description', 'state'])
            ->map(fn (Room $room): RoomSummary => new RoomSummary(
                (int) $room->id,
                (string) $room->name,
                (string) $room->description,
                (string) $room->state,
            ));
    }

    public function count(): int
    {
        return Room::query()->count();
    }
}
