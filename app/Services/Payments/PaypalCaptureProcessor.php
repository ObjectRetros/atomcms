<?php

namespace App\Services\Payments;

use App\Data\PaypalCapture;
use App\Exceptions\PaypalPaymentException;
use App\Models\Shop\WebsitePaypalTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class PaypalCaptureProcessor
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
            if (is_array($capture) && ($capture['status'] ?? null) === WebsitePaypalTransaction::STATUS_COMPLETED) {
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

        $result = DB::transaction(function () use ($orderId, $capture): string {
            $transaction = WebsitePaypalTransaction::where('transaction_id', $orderId)->lockForUpdate()->first();

            if ($transaction === null) {
                return 'missing';
            }

            if ($transaction->credited_at !== null) {
                return 'credited';
            }

            if ($transaction->amount !== $capture->amount || $transaction->currency !== $capture->currency) {
                $transaction->update([
                    'status' => WebsitePaypalTransaction::STATUS_REVIEW,
                    'description' => 'Captured amount or currency did not match the order.',
                ]);

                return 'mismatch';
            }

            $captureUsed = WebsitePaypalTransaction::where('capture_id', $capture->id)
                ->whereKeyNot($transaction->getKey())
                ->exists();

            if ($captureUsed) {
                $transaction->update([
                    'status' => WebsitePaypalTransaction::STATUS_REVIEW,
                    'description' => 'Capture ID was already assigned to another order.',
                ]);

                return 'mismatch';
            }

            $user = User::whereKey($transaction->user_id)->lockForUpdate()->first();

            if ($user === null) {
                $transaction->update([
                    'status' => WebsitePaypalTransaction::STATUS_REVIEW,
                    'description' => 'The transaction owner no longer exists.',
                ]);

                return 'mismatch';
            }

            $user->increment('website_balance', $capture->amount);
            $transaction->update([
                'capture_id' => $capture->id,
                'status' => WebsitePaypalTransaction::STATUS_COMPLETED,
                'description' => null,
                'credited_at' => now(),
            ]);

            return 'credited';
        }, attempts: 3);

        if ($result === 'mismatch') {
            Log::critical('PayPal capture requires manual review.', [
                'order_id' => $orderId,
                'capture_id' => $capture->id,
            ]);

            return false;
        }

        return $result === 'credited';
    }
}
