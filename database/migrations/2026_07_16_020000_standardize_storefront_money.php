<?php

use Brick\Money\Currency;
use Brick\Money\Money;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $factor = $this->minorUnitFactor();

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('website_balance')->default(0)->change();
        });

        Schema::table('website_shop_vouchers', function (Blueprint $table) {
            $table->unsignedBigInteger('amount')->change();
        });

        DB::table('users')->update(['website_balance' => DB::raw("website_balance * {$factor}")]);
        DB::table('website_shop_vouchers')->update(['amount' => DB::raw("amount * {$factor}")]);
        DB::table('website_paypal_transactions')->update(['currency' => DB::raw('UPPER(currency)')]);

        DB::table('website_paypal_transactions')
            ->select(['id', 'amount', 'currency'])
            ->orderBy('id')
            ->chunkById(500, function ($transactions): void {
                foreach ($transactions as $transaction) {
                    $amount = Money::of((string) $transaction->amount, (string) $transaction->currency)
                        ->getMinorAmount()
                        ->toInt();

                    DB::table('website_paypal_transactions')
                        ->where('id', $transaction->id)
                        ->update(['amount' => $amount]);
                }
            });

        Schema::table('website_paypal_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('amount')->change();
            $table->string('capture_id')->nullable()->unique()->after('transaction_id');
            $table->timestamp('credited_at')->nullable()->after('currency');
            $table->index(['status', 'created_at']);
        });

        DB::table('website_paypal_transactions')
            ->whereNull('status')
            ->where('amount', 0)
            ->update(['status' => 'LEGACY_CREATED']);

        DB::table('website_paypal_transactions')
            ->where('status', 'COMPLETED')
            ->update(['credited_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        $factor = $this->minorUnitFactor();

        if (
            DB::table('users')->whereRaw('MOD(website_balance, ?) != 0', [$factor])->exists()
            || DB::table('website_shop_vouchers')->whereRaw('MOD(amount, ?) != 0', [$factor])->exists()
        ) {
            throw new RuntimeException('Storefront balances contain fractional major units and cannot be rolled back safely.');
        }

        Schema::table('website_paypal_transactions', function (Blueprint $table) {
            $table->dropUnique(['capture_id']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropColumn(['capture_id', 'credited_at']);
            $table->float('amount')->change();
        });

        DB::table('website_paypal_transactions')
            ->where('status', 'LEGACY_CREATED')
            ->update(['status' => null]);

        DB::table('website_paypal_transactions')
            ->select(['id', 'amount', 'currency'])
            ->orderBy('id')
            ->chunkById(500, function ($transactions): void {
                foreach ($transactions as $transaction) {
                    $amount = Money::ofMinor((string) $transaction->amount, (string) $transaction->currency)
                        ->getAmount();

                    DB::table('website_paypal_transactions')
                        ->where('id', $transaction->id)
                        ->update(['amount' => (string) $amount]);
                }
            });

        DB::table('users')->update(['website_balance' => DB::raw("website_balance / {$factor}")]);
        DB::table('website_shop_vouchers')->update(['amount' => DB::raw("amount / {$factor}")]);

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('website_balance')->default(0)->change();
        });

        Schema::table('website_shop_vouchers', function (Blueprint $table) {
            $table->unsignedInteger('amount')->change();
        });
    }

    private function minorUnitFactor(): int
    {
        $currency = Currency::of(strtoupper((string) config('habbo.paypal.currency', 'USD')));

        return 10 ** $currency->getDefaultFractionDigits();
    }
};
