<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Game\Room;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function __invoke(Room $room): View
    {
        return view('room.index', [
            'room' => $room->load([
                'owner:id,username,look',
                'guild:id,room_id,name,description,badge',
            ]),
        ]);
    }
}
