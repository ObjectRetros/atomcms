<?php

namespace App\Emulator\Data;

/**
 * A currency the CMS understands, independent of how any given emulator stores
 * it. Drivers map these onto their own schema (a typed row, a column, ...).
 */
enum Currency
{
    case Credits;
    case Duckets;
    case Diamonds;
    case Points;
}
