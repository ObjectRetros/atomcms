<?php

namespace App\Enums;

enum RareValueTypes : string
{
    case REGULAR = 'regular';
    case RARE = 'rare';
    case ULTRA_RARE = 'ultra rare';
    case LIMITED_EDITION = 'limited edition';
}