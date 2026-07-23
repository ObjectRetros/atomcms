<?php

namespace App\Services\Payments;

use App\Contracts\PaypalGateway;
use App\Exceptions\PaypalPaymentException;
use Illuminate\Http\Request;
use RuntimeException;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Throwable;

final class SrmklivePaypalGateway implements PaypalGateway
{
    private bool $authenticated = false;

    public function __construct(private readonly PayPalClient $client) {}

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    public function createOrder(array $data): array
    {
        return $this->arrayResponse($this->client()->createOrder($data));
    }

    /**
     * @return array<string, mixed>
     */
    public function captureOrder(string $orderId, string $idempotencyKey): array
    {
        $response = $this->client()
            ->setRequestHeader('PayPal-Request-Id', $idempotencyKey)
            ->capturePaymentOrder($orderId);

        return $this->arrayResponse($response);
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * Authenticate lazily on first use, so resolving the gateway never blocks
     * on PayPal's OAuth endpoint and an outage surfaces as a payment error.
     */
    private function client(): PayPalClient
    {
        if ($this->authenticated) {
            return $this->client;
        }

        try {
            $response = $this->client->getAccessToken();
        } catch (Throwable $exception) {
            throw PaypalPaymentException::gatewayFailure($exception);
        }

        if (! is_array($response) || ! isset($response['access_token'])) {
            throw PaypalPaymentException::gatewayFailure();
        }

        $this->authenticated = true;

        return $this->client;
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
