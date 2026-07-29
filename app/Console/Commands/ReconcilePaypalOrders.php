<?php

namespace App\Console\Commands;

use App\Enums\PaypalTransactionStatus;
use App\Exceptions\PaypalPaymentException;
use App\Models\Shop\WebsitePaypalTransaction;
use App\Services\Payments\PaypalPaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcilePaypalOrders extends Command
{
    protected $signature = 'paypal:reconcile {--limit=100 : Maximum pending orders to inspect}';

    protected $description = 'Reconcile pending PayPal orders and credit completed captures';

    public function handle(PaypalPaymentService $payments): int
    {
        $limit = max(1, min(1000, (int) $this->option('limit')));
        $failures = 0;

        $transactions = WebsitePaypalTransaction::query()
            ->whereNull('credited_at')
            ->whereNotIn('status', [PaypalTransactionStatus::Cancelled->value, PaypalTransactionStatus::Review->value])
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('last_reconciled_at')
            ->oldest()
            ->limit($limit)
            ->get();

        foreach ($transactions as $transaction) {
            try {
                $payments->reconcile($transaction);
            } catch (PaypalPaymentException $exception) {
                $failures++;

                Log::warning('PayPal order reconciliation failed.', [
                    'order_id' => $transaction->transaction_id,
                    'exception_class' => $exception::class,
                ]);
            } finally {
                WebsitePaypalTransaction::whereKey($transaction->getKey())->update([
                    'last_reconciled_at' => now(),
                ]);
            }
        }

        $this->components->info(sprintf(
            'Inspected %d pending PayPal order(s); %d failed.',
            $transactions->count(),
            $failures,
        ));

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }
}
