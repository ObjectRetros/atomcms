<?php

namespace App\Services\Payments;

use App\Contracts\PaypalGateway;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use RuntimeException;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class SrmklivePaypalGateway implements PaypalGateway
{
    private ?PayPalClient $client = null;

    public function __construct(private readonly Container $container) {}

    public function createOrder(array $data): array
    {
        return $this->arrayResponse($this->client()->createOrder($data));
    }

    public function captureOrder(string $orderId, string $idempotencyKey): array
    {
        $response = $this->client()
            ->setRequestHeader('PayPal-Request-Id', $idempotencyKey)
            ->capturePaymentOrder($orderId);

        return $this->arrayResponse($response);
    }

    public function showOrder(string $orderId): array
    {
        return $this->arrayResponse($this->client()->showOrderDetails($orderId));
    }

    public function verifyWebhook(Request $request, string $webhookId): bool
    {
        $response = $this->client()
            ->setWebHookID($webhookId)
            ->verifyIPN($request);

        return is_array($response) && ($response['verification_status'] ?? null) === 'SUCCESS';
    }

    private function client(): PayPalClient
    {
        return $this->client ??= $this->container->make(PayPalClient::class);
    }

    /**
     * @return array<string, mixed>
     */
    private function arrayResponse(mixed $response): array
    {
        if (! is_array($response)) {
            throw new RuntimeException('PayPal returned an invalid response.');
        }

        return $response;
    }
}
