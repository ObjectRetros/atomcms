<?php

namespace App\Data;

final readonly class PurchaseReceiptData
{
    public function __construct(public int $purchaseId, public string $packageName, public string $recipientUsername, public int $chargedMinor, public string $currency) {}
}
