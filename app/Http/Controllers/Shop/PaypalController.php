<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountTopupFormRequest;
use App\Models\Shop\WebsitePaypalTransaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Symfony\Component\HttpFoundation\Response;

class PaypalController extends Controller
{
    private const STATUS_CANCELLED = 'CANCELLED';

    private const STATUS_COMPLETED = 'COMPLETED';

    public function __construct(private readonly PayPalClient $provider) {}

    public function process(AccountTopupFormRequest $request): Response|RedirectResponse
    {
        $response = $this->provider->createOrder($this->buildOrderData($request->integer('amount')));

        if (! is_array($response)) {
            return $this->orderCreationFailed(['message' => 'PayPal returned an invalid order response.']);
        }

        $orderId = $response['id'] ?? null;
        $approvalUrl = is_string($orderId) ? $this->approvalUrl($response['links'] ?? null) : null;

        if ($approvalUrl === null) {
            return $this->orderCreationFailed($response);
        }

        $request->user()->transactions()->create([
            'transaction_id' => $orderId,
            'amount' => 0,
        ]);

        return redirect()->away($approvalUrl);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildOrderData(int $amount): array
    {
        return [
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => route('paypal.successful-transaction'),
                'cancel_url' => route('paypal.cancelled-transaction'),
                'brand_name' => setting('hotel_name'),
                'landing_page' => 'BILLING',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'CONTINUE',
            ],
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => config('habbo.paypal.currency'),
                        'value' => (string) $amount,
                    ],
                ],
            ],
        ];
    }

    private function approvalUrl(mixed $links): ?string
    {
        if (! is_array($links)) {
            return null;
        }

        foreach ($links as $link) {
            if (! is_array($link) || ($link['rel'] ?? null) !== 'approve') {
                continue;
            }

            $href = $link['href'] ?? null;

            if (is_string($href)) {
                return $href;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function orderCreationFailed(array $response): RedirectResponse
    {
        Log::error('Error creating PayPal order', ['response' => $response]);

        return to_route('shop.index')->withErrors(['message' => $response['message'] ?? __('Something went wrong')]);
    }

    public function successful(Request $request): Response
    {
        $request->validate([
            'token' => 'required',
        ]);

        $user = $request->user();

        $transaction = $user->transactions()->where('transaction_id', $request['token'])->first();
        if ($transaction === null) {
            return to_route('shop.index')->withErrors(['message' => __('Something went wrong, please try again later')]);
        }

        // Idempotency: never capture or credit an order that already completed.
        if ($transaction->status === self::STATUS_COMPLETED) {
            return to_route('shop.index')->with('success', __('Transaction successful'));
        }

        $response = $this->provider->capturePaymentOrder($request['token']);

        if (! is_array($response)) {
            $this->recordFailure($transaction, ['message' => 'PayPal returned an invalid capture response.']);

            return to_route('shop.index')->withErrors(['message' => __('Something went wrong, please check your paypal account to make sure nothing was deducted and try again')]);
        }

        $capture = data_get($response, 'purchase_units.0.payments.captures.0');

        if (! is_string($response['status'] ?? null) || ! is_array($capture)) {
            $this->recordFailure($transaction, $response);

            return to_route('shop.index')->withErrors(['message' => __('Something went wrong, please check your paypal account to make sure nothing was deducted and try again')]);
        }

        if ($response['status'] !== self::STATUS_COMPLETED) {
            $transaction->update(['status' => $capture['status'] ?? $response['status'], 'amount' => 0]);

            return to_route('shop.index')->withErrors(['message' => $response['message'] ?? __('Something went wrong')]);
        }

        if (data_get($capture, 'amount.currency_code') !== config('habbo.paypal.currency')) {
            Log::error('PayPal currency mismatch', ['response' => $response]);

            return to_route('shop.index')->withErrors(['message' => __('Something went wrong, please try again later')]);
        }

        $this->creditCompletedOrder($user, $transaction->getKey(), $capture);

        return to_route('shop.index')->with('success', __('Transaction successful'));
    }

    /**
     * Mark the order completed and credit the balance exactly once, even under
     * concurrent return-url requests, by locking the transaction row first.
     *
     * @param  array<string, mixed>  $capture
     */
    private function creditCompletedOrder(User $user, int|string $transactionKey, array $capture): void
    {
        DB::transaction(function () use ($user, $transactionKey, $capture) {
            $transaction = $user->transactions()->whereKey($transactionKey)->lockForUpdate()->first();

            if ($transaction === null || $transaction->status === self::STATUS_COMPLETED) {
                return;
            }

            $transaction->update([
                'status' => $capture['status'],
                'amount' => $capture['amount']['value'],
                'currency' => $capture['amount']['currency_code'],
            ]);

            $user->increment('website_balance', (int) $capture['amount']['value']);
        });
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function recordFailure(WebsitePaypalTransaction $transaction, array $response): void
    {
        $details = data_get($response, 'details.0', data_get($response, 'error.details.0'));

        $transaction->update([
            'status' => $response['name'] ?? 'FAILED',
            'description' => $details
                ? sprintf('%s - %s', $details['issue'] ?? '', $details['description'] ?? '')
                : ($response['message'] ?? 'Unknown PayPal error'),
            'amount' => 0,
        ]);

        Log::error('PayPal capture failed', ['response' => $response]);
    }

    public function cancelled(Request $request): Response
    {
        $request->validate([
            'token' => 'required',
        ]);

        $transaction = $request->user()->transactions()->where('transaction_id', $request['token'])->first();
        if ($transaction !== null) {
            $transaction->update([
                'status' => self::STATUS_CANCELLED,
                'description' => 'The user cancelled the transaction',
            ]);
        }

        return to_route('shop.index')->withErrors(
            ['message' => __('You have canceled the transaction')],
        );
    }
}
