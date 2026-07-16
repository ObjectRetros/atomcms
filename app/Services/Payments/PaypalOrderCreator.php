<?php

namespace App\Services\Payments;

use App\Contracts\PaypalGateway;
use App\Exceptions\PaypalPaymentException;
use App\Models\Shop\WebsitePaypalTransaction;
use App\Models\User;
use App\Support\StorefrontMoney;
use Throwable;

class PaypalOrderCreator
{
    public function __construct(private readonly PaypalGateway $gateway) {}

    public function create(User $user, int $majorAmount): string
    {
        $money = StorefrontMoney::fromMajor($majorAmount);

        try {
            $response = $this->gateway->createOrder($this->orderData((string) $money->getAmount()));
        } catch (Throwable $exception) {
            throw PaypalPaymentException::gatewayFailure($exception);
        }

        $orderId = $response['id'] ?? null;
        $approvalUrl = $this->approvalUrl($response['links'] ?? null);

        if (! is_string($orderId) || $orderId === '' || strlen($orderId) > 255 || $approvalUrl === null) {
            throw PaypalPaymentException::invalidResponse();
        }

        $user->transactions()->create([
            'transaction_id' => $orderId,
            'status' => WebsitePaypalTransaction::STATUS_CREATED,
            'amount' => StorefrontMoney::minorAmount($money),
            'currency' => $money->getCurrency()->getCurrencyCode(),
        ]);

        return $approvalUrl;
    }

    /**
     * @return array<string, mixed>
     */
    private function orderData(string $amount): array
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
