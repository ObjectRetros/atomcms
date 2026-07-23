<?php

use App\Enums\PaypalTransactionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Legacy checkout code stored raw PayPal order/capture statuses and even
     * API error names in the status column. The column is now backed by the
     * PaypalTransactionStatus enum, so park every unknown value in REVIEW
     * while preserving the original status in the description.
     */
    public function up(): void
    {
        $known = array_column(PaypalTransactionStatus::cases(), 'value');

        DB::table('website_paypal_transactions')
            ->whereNotNull('status')
            ->whereNotIn('status', $known)
            ->update([
                'description' => DB::raw("CONCAT('Legacy gateway status: ', status)"),
                'status' => PaypalTransactionStatus::Review->value,
            ]);
    }

    public function down(): void
    {
        // Irreversible: the original status strings only survive inside the
        // description text of the affected rows.
    }
};
