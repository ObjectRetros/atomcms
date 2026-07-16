<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

final class CommunityCache
{
    public const STAFF_IDS = 'community.staff.ids';

    public const STAFF_POSITIONS_PRIVILEGED = 'community.staff.positions.privileged';

    public const STAFF_POSITIONS_PUBLIC = 'community.staff.positions.public';

    public const TEAMS = 'community.teams';

    public static function staffPositionsKey(bool $includeHidden): string
    {
        return $includeHidden
            ? self::STAFF_POSITIONS_PRIVILEGED
            : self::STAFF_POSITIONS_PUBLIC;
    }

    public static function forgetStaffPositions(): void
    {
        Cache::forget(self::STAFF_POSITIONS_PRIVILEGED);
        Cache::forget(self::STAFF_POSITIONS_PUBLIC);
    }

    public static function forgetStaffIds(): void
    {
        Cache::forget(self::STAFF_IDS);
    }

    public static function forgetTeams(): void
    {
        Cache::forget(self::TEAMS);
    }

    public static function forgetAll(): void
    {
        self::forgetStaffPositions();
        self::forgetStaffIds();
        self::forgetTeams();
    }
}
