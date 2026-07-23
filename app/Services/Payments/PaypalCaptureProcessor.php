<?php

namespace App\Services\Payments;

use App\Data\PaypalCapture;
use App\Enums\CaptureOutcome;
use App\Enums\PaypalTransactionStatus;
use App\Exceptions\PaypalPaymentException;
use App\Models\Shop\WebsitePaypalTransaction;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

final readonly class PaypalCaptureProcessor
{
    /**
     * @param  array<string, mixed>  $response
     */
    public function applyOrderResponse(string $orderId, array $response): bool
    {
        if (isset($response['id']) && $response['id'] !== $orderId) {
            throw PaypalPaymentException::invalidResponse();
        }

        $captures = data_get($response, 'purchase_units.0.payments.captures');

        if (! is_array($captures)) {
            return false;
        }

        foreach ($captures as $capture) {
            if (is_array($capture) && ($capture['status'] ?? null) === PaypalTransactionStatus::Completed->value) {
                return $this->applyCompletedCapture($orderId, $capture);
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function applyCompletedCapture(string $orderId, array $payload): bool
    {
        try {
            $capture = PaypalCapture::fromPayload($payload);
        } catch (InvalidArgumentException $exception) {
            throw new PaypalPaymentException($exception->getMessage(), previous: $exception);
        }

        try {
            $outcome = DB::transaction(function () use ($orderId, $capture): CaptureOutcome {
                return $this->creditLockedTransaction($orderId, $capture);
            }, attempts: 3);
        } catch (UniqueConstraintViolationException) {
            // The exists() pre-check raced a concurrent webhook: the unique
            // index on capture_id rejected the second credit. Hold the order
            // for review instead of surfacing a 500 to PayPal's retries.
            $this->holdForReview($orderId, 'Capture ID was already assigned to another order.');

            $outcome = CaptureOutcome::Mismatch;
        }

        if ($outcome === CaptureOutcome::Mismatch) {
            Log::critical('PayPal capture requires manual review.', [
                'order_id' => $orderId,
                'capture_id' => $capture->id,
            ]);

            return false;
        }

        return $outcome === CaptureOutcome::Credited;
    }

    private function creditLockedTransaction(string $orderId, PaypalCapture $capture): CaptureOutcome
    {
        $transaction = WebsitePaypalTransaction::where('transaction_id', $orderId)->lockForUpdate()->first();

        if ($transaction === null) {
            return CaptureOutcome::Missing;
        }

        if ($transaction->credited_at !== null) {
            return CaptureOutcome::Credited;
        }

        if ($transaction->amount !== $capture->amount || $transaction->currency !== $capture->currency) {
            $transaction->update([
                'status' => PaypalTransactionStatus::Review,
                'description' => 'Captured amount or currency did not match the order.',
            ]);

            return CaptureOutcome::Mismatch;
        }

        $captureUsed = WebsitePaypalTransaction::where('capture_id', $capture->id)
            ->whereKeyNot($transaction->getKey())
            ->exists();

        if ($captureUsed) {
            $transaction->update([
                'status' => PaypalTransactionStatus::Review,
                'description' => 'Capture ID was already assigned to another order.',
            ]);

            return CaptureOutcome::Mismatch;
        }

        $user = User::whereKey($transaction->user_id)->lockForUpdate()->first();

        if ($user === null) {
            $transaction->update([
                'status' => PaypalTransactionStatus::Review,
                'description' => 'The transaction owner no longer exists.',
            ]);

            return CaptureOutcome::Mismatch;
        }

        $user->increment('website_balance', $capture->amount);
        $transaction->update([
            'capture_id' => $capture->id,
            'status' => PaypalTransactionStatus::Completed,
            'description' => null,
            'credited_at' => now(),
        ]);

        return CaptureOutcome::Credited;
    }

    private function holdForReview(string $orderId, string $reason): void
    {
        WebsitePaypalTransaction::where('transaction_id', $orderId)
            ->whereNull('credited_at')
            ->update([
                'status' => PaypalTransactionStatus::Review->value,
                'description' => $reason,
            ]);
    }
}
