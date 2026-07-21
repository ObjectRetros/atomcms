<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface PaypalGateway
{
    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    public function createOrder(array $data): array;

    /** @return array<string, mixed> */
    public function captureOrder(string $orderId, string $idempotencyKey): array;

    /** @return array<string, mixed> */
    public function showOrder(string $orderId): array;

    public function verifyWebhook(Request $request, string $webhookId): bool;
}
