<?php

namespace App\Services\Payments;

use App\Contracts\PaypalGateway;
use App\Data\PaypalOrderData;
use App\Enums\PaypalTransactionStatus;
use App\Exceptions\PaypalPaymentException;
use App\Models\User;
use App\Support\StorefrontMoney;
use Throwable;

final readonly class PaypalOrderCreator
{
    public function __construct(private readonly PaypalGateway $gateway) {}

    public function create(User $user, int $majorAmount): string
    {
        return $this->createWithReceipt($user, $majorAmount)->approvalUrl;
    }

    public function createWithReceipt(User $user, int $majorAmount, ?string $idempotencyKey = null): PaypalOrderData
    {
        $money = StorefrontMoney::fromMajor($majorAmount);

        try {
            $response = ($idempotencyKey === null ? $this->gateway->createOrder($this->orderData((string) $money->getAmount())) : $this->gateway->createOrder($this->orderData((string) $money->getAmount(), $idempotencyKey), $idempotencyKey));
        } catch (Throwable $exception) {
            throw PaypalPaymentException::gatewayFailure($exception);
        }

        $orderId = $response['id'] ?? null;
        $approvalUrl = $this->approvalUrl($response['links'] ?? null);

        if (! is_string($orderId) || $orderId === '' || strlen($orderId) > 255 || $approvalUrl === null) {
            throw PaypalPaymentException::invalidResponse();
        }

        $user->transactions()->firstOrCreate(['transaction_id' => $orderId], [
            'status' => PaypalTransactionStatus::Created,
            'amount' => StorefrontMoney::minorAmount($money),
            'currency' => $money->getCurrency()->getCurrencyCode(),
        ]);

        return new PaypalOrderData($orderId, $approvalUrl, StorefrontMoney::minorAmount($money), $money->getCurrency()->getCurrencyCode());
    }

    /**
     * @return array<string, mixed>
     */
    private function orderData(string $amount, ?string $reference = null): array
    {
        return [
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => route('paypal.successful-transaction'),
                'cancel_url' => route('paypal.cancelled-transaction'),
                'brand_name' => setting('hotel_name'),
                'landing_page' => 'BILLING',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'PAY_NOW',
            ],
            'purchase_units' => [[
                ...($reference === null ? [] : ['custom_id' => $reference]),
                'amount' => [
                    'currency_code' => StorefrontMoney::currencyCode(),
                    'value' => $amount,
                ],
            ]],
        ];
    }

    private function approvalUrl(mixed $links): ?string
    {
        if (! is_array($links)) {
            return null;
        }

        foreach ($links as $link) {
            if (! is_array($link) || ($link['rel'] ?? null) !== 'approve' || ! is_string($link['href'] ?? null)) {
                continue;
            }

            $parts = parse_url($link['href']);
            $host = is_array($parts) ? strtolower((string) ($parts['host'] ?? '')) : '';

            if (($parts['scheme'] ?? null) === 'https' && ($host === 'paypal.com' || str_ends_with($host, '.paypal.com'))) {
                return $link['href'];
            }
        }

        return null;
    }
}
