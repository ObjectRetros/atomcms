<?php

namespace App\Enums;

/**
 * Result of applying a completed PayPal capture to a stored transaction.
 */
enum CaptureOutcome: string
{
    /** No stored transaction matches the capture's order id. */
    case Missing = 'missing';

    /** The transaction is credited (now or by an earlier attempt). */
    case Credited = 'credited';

    /** The capture disagrees with the order and is held for manual review. */
    case Mismatch = 'mismatch';
}
