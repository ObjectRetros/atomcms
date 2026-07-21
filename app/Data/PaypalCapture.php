<?php

namespace App\Data;

use App\Support\StorefrontMoney;
use Brick\Math\Exception\MathException;
use Brick\Money\Exception\MoneyException;
use InvalidArgumentException;

final readonly class PaypalCapture
{
    public function __construct(
        public string $id,
        public int $amount,
        public string $currency,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        $id = $payload['id'] ?? null;
        $status = $payload['status'] ?? null;
        $value = data_get($payload, 'amount.value');
        $currency = data_get($payload, 'amount.currency_code');

        if (
            ! is_string($id)
            || $id === ''
            || strlen($id) > 255
            || $status !== 'COMPLETED'
            || ! is_string($value)
            || ! is_string($currency)
        ) {
            throw new InvalidArgumentException('PayPal capture data is incomplete.');
        }

        try {
            $money = StorefrontMoney::fromDecimal($value, $currency);
            $minorAmount = StorefrontMoney::minorAmount($money);
        } catch (MathException|MoneyException $exception) {
            throw new InvalidArgumentException('PayPal capture amount is invalid.', previous: $exception);
        }

        return new self(
            id: $id,
            amount: $minorAmount,
            currency: $money->getCurrency()->getCurrencyCode(),
        );
    }
}
