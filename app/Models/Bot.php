<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    /**
     * The table associated with the model.
     * This maps to the Arcturus Morningstar 'bots' table.
     */
    protected $table = 'bots';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'chat_auto' => 'boolean',
        'chat_random' => 'boolean',
        'freeroam' => 'boolean',
        'z' => 'float',
    ];

    /**
     * Get the room this bot belongs to.
     */
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    /**
     * Get the owner of this bot.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if a given position is within interaction distance.
     */
    public function isWithinDistance(int $playerX, int $playerY, int $maxDistance = 1): bool
    {
        $distance = max(abs($this->x - $playerX), abs($this->y - $playerY));

        return $distance <= $maxDistance;
    }
}
