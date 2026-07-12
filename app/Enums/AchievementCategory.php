<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AchievementCategory: string
{
    use HasOptions;

    case Identity = 'identity';
    case Explore = 'explore';
    case Music = 'music';
    case Social = 'social';
    case Games = 'games';
    case RoomBuilder = 'room_builder';
    case Pets = 'pets';
    case Tools = 'tools';
    case Events = 'events';
}
