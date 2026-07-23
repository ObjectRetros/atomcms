<?php

namespace App\Enums;

enum PaypalTransactionStatus: string
{
    case Created = 'CREATED';
    case LegacyCreated = 'LEGACY_CREATED';
    case Completed = 'COMPLETED';
    case Cancelled = 'CANCELLED';
    case Voided = 'VOIDED';
    case Review = 'REVIEW';
}
