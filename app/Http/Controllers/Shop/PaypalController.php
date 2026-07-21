<?php

namespace App\Http\Controllers\Shop;

use App\Exceptions\PaypalPaymentException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountTopupFormRequest;
use App\Models\Shop\WebsitePaypalTransaction;
use App\Services\Payments\PaypalPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaypalController extends Controller
{
    public function process(AccountTopupFormRequest $request, PaypalPaymentService $payments): RedirectResponse
    {
        try {
            $approvalUrl = $payments->createOrder($request->user(), $request->integer('amount'));
        } catch (PaypalPaymentException $exception) {
            Log::warning('PayPal order creation failed.', [
                'user_id' => $request->user()->getKey(),
                'exception_class' => $exception::class,
            ]);

            return $this->failure();
        }
        return redirect()->away($approvalUrl);
    }

    public function successful(Request $request, PaypalPaymentService $payments): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:255'],
        ]);

        $transaction = $request->user()
            ->transactions()
            ->where('transaction_id', $validated['token'])
            ->first();

        if ($transaction === null) {
            return $this->failure();
        }

        if ($transaction->credited_at !== null || $transaction->status === WebsitePaypalTransaction::STATUS_COMPLETED) {
            return $this->success();
        }

        try {
            $completed = $payments->capture($transaction);
        } catch (PaypalPaymentException $exception) {
            Log::warning('PayPal capture could not be completed on return.', [
                'order_id' => $transaction->transaction_id,
                'exception_class' => $exception::class,
            ]);

            return $this->pending();
        }

        return $completed ? $this->success() : $this->pending();
    }

    public function cancelled(Request $request, PaypalPaymentService $payments): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:255'],
        ]);

        $transaction = $request->user()
            ->transactions()
            ->where('transaction_id', $validated['token'])
            ->first();

        if ($transaction !== null) {
            $payments->cancel($transaction);
        }

        return to_route('shop.index')->withErrors([
            'message' => __('You have canceled the transaction'),
        ]);
    }

    private function success(): RedirectResponse
    {
        return to_route('shop.index')->with('success', __('Transaction successful'));
    }

    private function pending(): RedirectResponse
    {
        return to_route('shop.index')->withErrors([
            'message' => __('Your payment is still being verified. Your balance will update automatically.'),
        ]);
    }

    private function failure(): RedirectResponse
    {
        return to_route('shop.index')->withErrors([
            'message' => __('Something went wrong, please try again later'),
        ]);
    }
}
