<?php

namespace App\Emulator\Data;

/**
 * A player statistic the CMS understands, independent of how any emulator
 * stores it. Drivers map these onto their own columns/tables.
 */
enum Stat
{
    case OnlineTime;
    case RespectsReceived;
    case AchievementScore;
}
