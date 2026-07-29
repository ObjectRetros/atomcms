<?php

namespace App\Services\Payments;

use App\Contracts\PaypalGateway;
use App\Enums\PaypalTransactionStatus;
use App\Exceptions\PaypalPaymentException;
use App\Models\Shop\WebsitePaypalTransaction;
use App\Models\User;
use App\Support\StorefrontMoney;
use Brick\Math\Exception\MathException;
use Brick\Money\Exception\MoneyException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class PaypalPaymentService
{
    private const WEBHOOK_CAPTURE_COMPLETED = 'PAYMENT.CAPTURE.COMPLETED';

    private const WEBHOOK_ORDER_APPROVED = 'CHECKOUT.ORDER.APPROVED';

    public function __construct(
        private readonly PaypalGateway $gateway,
        private readonly PaypalOrderCreator $orders,
        private readonly PaypalCaptureProcessor $captures,
    ) {}

    public function createOrder(User $user, int $majorAmount): string
    {
        return $this->orders->create($user, $majorAmount);
    }

    public function capture(WebsitePaypalTransaction $transaction): bool
    {
        if ($transaction->credited_at !== null) {
            return true;
        }

        try {
            $transaction = $this->prepareLegacyTransaction($transaction);
            $response = $this->gateway->captureOrder(
                $transaction->transaction_id,
                'atom-capture-' . $transaction->transaction_id,
            );

            if ($this->captures->applyOrderResponse($transaction->transaction_id, $response)) {
                return true;
            }

            return $this->inspectOrder($transaction->transaction_id, captureApproved: false);
        } catch (PaypalPaymentException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw PaypalPaymentException::gatewayFailure($exception);
        }
    }

    public function reconcile(WebsitePaypalTransaction $transaction): bool
    {
        if ($transaction->credited_at !== null) {
            return true;
        }

        try {
            return $this->inspectOrder($transaction->transaction_id, captureApproved: true);
        } catch (PaypalPaymentException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw PaypalPaymentException::gatewayFailure($exception);
        }
    }

    /**
     * @param  array<string, mixed>  $event
     */
    public function handleWebhook(array $event): void
    {
        $eventType = $event['event_type'] ?? null;
        $resource = $event['resource'] ?? null;

        if (! is_string($eventType) || ! is_array($resource)) {
            throw PaypalPaymentException::invalidResponse();
        }

        if ($eventType === self::WEBHOOK_ORDER_APPROVED) {
            $orderId = $resource['id'] ?? null;

            if (is_string($orderId) && $orderId !== '') {
                $transaction = WebsitePaypalTransaction::where('transaction_id', $orderId)->first();

                if ($transaction !== null) {
                    $this->capture($transaction);
                }
            }

            return;
        }

        if ($eventType === self::WEBHOOK_CAPTURE_COMPLETED) {
            $orderId = data_get($resource, 'supplementary_data.related_ids.order_id');

            if (is_string($orderId) && $orderId !== '') {
                $this->captures->applyCompletedCapture($orderId, $resource);
            }
        }
    }

    public function cancel(WebsitePaypalTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $locked = WebsitePaypalTransaction::whereKey($transaction->getKey())->lockForUpdate()->first();

            if ($locked !== null && $locked->credited_at === null) {
                $locked->update([
                    'status' => PaypalTransactionStatus::Cancelled,
                    'description' => 'The user cancelled the transaction.',
                ]);
            }
        });
    }

    private function inspectOrder(string $orderId, bool $captureApproved): bool
    {
        $response = $this->gateway->showOrder($orderId);

        if ($this->captures->applyOrderResponse($orderId, $response)) {
            return true;
        }

        $status = $response['status'] ?? null;

        if ($captureApproved && $status === 'APPROVED') {
            $transaction = WebsitePaypalTransaction::where('transaction_id', $orderId)->first();

            return $transaction !== null && $this->capture($transaction);
        }

        $mapped = is_string($status) ? PaypalTransactionStatus::tryFrom($status) : null;

        if ($mapped === PaypalTransactionStatus::Cancelled || $mapped === PaypalTransactionStatus::Voided) {
            WebsitePaypalTransaction::where('transaction_id', $orderId)
                ->whereNull('credited_at')
                ->update(['status' => $mapped->value]);
        }

        return false;
    }

    private function prepareLegacyTransaction(WebsitePaypalTransaction $transaction): WebsitePaypalTransaction
    {
        if ($transaction->status !== PaypalTransactionStatus::LegacyCreated) {
            return $transaction;
        }

        $response = $this->gateway->showOrder($transaction->transaction_id);

        if (($response['id'] ?? null) !== $transaction->transaction_id) {
            throw PaypalPaymentException::invalidResponse();
        }

        $value = data_get($response, 'purchase_units.0.amount.value');
        $currency = data_get($response, 'purchase_units.0.amount.currency_code');

        if (! is_string($value) || ! is_string($currency)) {
            throw PaypalPaymentException::invalidResponse();
        }

        try {
            $money = StorefrontMoney::fromDecimal($value, $currency);
            $amount = StorefrontMoney::minorAmount($money);
        } catch (MathException|MoneyException) {
            throw PaypalPaymentException::invalidResponse();
        }

        $maximum = StorefrontMoney::minorAmount(StorefrontMoney::fromMajor(250));

        if (
            $money->getCurrency()->getCurrencyCode() !== StorefrontMoney::currencyCode()
            || $amount < 1
            || $amount > $maximum
        ) {
            throw PaypalPaymentException::captureMismatch();
        }

        DB::transaction(function () use ($transaction, $amount, $money): void {
            WebsitePaypalTransaction::whereKey($transaction->getKey())
                ->where('status', PaypalTransactionStatus::LegacyCreated->value)
                ->where('amount', 0)
                ->update([
                    'status' => PaypalTransactionStatus::Created->value,
                    'amount' => $amount,
                    'currency' => $money->getCurrency()->getCurrencyCode(),
                ]);
        });

        return $transaction->refresh();
    }
}
