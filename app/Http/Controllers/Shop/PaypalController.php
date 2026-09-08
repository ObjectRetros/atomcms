<?php

namespace App\Http\Controllers\Shop;

use App\Exceptions\PaypalPaymentException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountTopupFormRequest;
use App\Models\Shop\WebsitePaypalTransaction;
use App\Services\Payments\PaypalPaymentService;
use App\Support\AuthenticatedUser;
use App\Support\FrontendUrls;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaypalController extends Controller
{
    public function process(AccountTopupFormRequest $request, PaypalPaymentService $payments): RedirectResponse
    {
        $user = AuthenticatedUser::from($request);

        try {
            $approvalUrl = $payments->createOrder($user, $request->integer('amount'));
        } catch (PaypalPaymentException $exception) {
            Log::warning('PayPal order creation failed.', [
                'user_id' => $user->getKey(),
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

        $transaction = AuthenticatedUser::from($request)
            ->transactions()
            ->where('transaction_id', $validated['token'])
            ->first();

        if ($transaction === null) {
            return $this->failure();
        }

        if ($transaction->credited_at !== null || $transaction->status === WebsitePaypalTransaction::STATUS_COMPLETED) {
            return $this->success($transaction->transaction_id);
        }

        try {
            $completed = $payments->capture($transaction);
        } catch (PaypalPaymentException $exception) {
            Log::warning('PayPal capture could not be completed on return.', [
                'order_id' => $transaction->transaction_id,
                'exception_class' => $exception::class,
            ]);

            return $this->pending($transaction->transaction_id);
        }

        return $completed ? $this->success($transaction->transaction_id) : $this->pending($transaction->transaction_id);
    }

    public function cancelled(Request $request, PaypalPaymentService $payments): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:255'],
        ]);

        $transaction = AuthenticatedUser::from($request)
            ->transactions()
            ->where('transaction_id', $validated['token'])
            ->first();

        if ($transaction !== null) {
            $payments->cancel($transaction);
        }

        return redirect($this->resultUrl($transaction?->transaction_id, 'cancelled'))->withErrors([
            'message' => __('You have canceled the transaction'),
        ]);
    }

    private function success(string $order): RedirectResponse
    {
        return redirect($this->resultUrl($order, 'completed'))->with('success', __('Transaction successful'));
    }

    private function pending(string $order): RedirectResponse
    {
        return redirect($this->resultUrl($order, 'pending'))->withErrors([
            'message' => __('Your payment is still being verified. Your balance will update automatically.'),
        ]);
    }

    private function failure(): RedirectResponse
    {
        return redirect($this->resultUrl(null, 'failed'))->withErrors([
            'message' => __('Something went wrong, please try again later'),
        ]);
    }

    private function resultUrl(?string $order, string $status): string
    {
        $query = config('atom.mode') === 'headless'
            ? array_filter(['order' => $order, 'payment_status' => $status], fn ($value): bool => $value !== null)
            : [];

        return app(FrontendUrls::class)->route('shop.index', query: $query);
    }
}
