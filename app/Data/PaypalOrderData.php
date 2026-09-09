<?php

namespace App\Data;

final readonly class PaypalOrderData
{
    public function __construct(public string $orderId, public string $approvalUrl, public int $amount, public string $currency) {}
}
