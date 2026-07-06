<?php

namespace App\Emulator\Data;

/**
 * CMS features that depend on emulator-specific database schema. Anything not
 * behind a Feature works 1:1 on every driver (currencies, badges, stats, bans
 * and furniture go through repositories); a Feature marks surface that only
 * exists - or is only implemented so far - for certain emulators, and is
 * hidden with an early return everywhere else.
 */
enum Feature: string
{
    case RoomChatlogs = 'room-chatlogs';
    case PrivateChatlogs = 'private-chatlogs';
    case CommandLogs = 'command-logs';
    case EmulatorSettings = 'emulator-settings';
    case EmulatorTexts = 'emulator-texts';
    case Wordfilter = 'wordfilter';
    case BanManagement = 'ban-management';
    case CatalogManagement = 'catalog-management';
    case RareValues = 'rare-values';
    case NameChangePermission = 'name-change-permission';
    case EmulatorUserSettings = 'emulator-user-settings';
    case UserBadgeManagement = 'user-badge-management';
}
