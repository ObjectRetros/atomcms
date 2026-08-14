<?php

namespace App\Enums;

/**
 * How a database looks to Atom before it imports anything into it.
 */
enum HotelSchemaState: string
{
    /** No tables at all - Atom may import the bundled base database. */
    case Fresh = 'fresh';

    /** Only tables Atom creates itself - the emulator schema is still missing. */
    case AtomOnly = 'atom-only';

    /** A recognised Arcturus/Polaris hotel that is already set up - import nothing. */
    case Hotel = 'hotel';

    /** Non-empty and unrecognised - it belongs to something else. */
    case Unknown = 'unknown';

    /**
     * Whether importing the bundled Arcturus dump - which drops and recreates
     * every table it ships - can be done without destroying someone's data.
     */
    public function safeToImport(): bool
    {
        return $this === self::Fresh || $this === self::AtomOnly;
    }
}
