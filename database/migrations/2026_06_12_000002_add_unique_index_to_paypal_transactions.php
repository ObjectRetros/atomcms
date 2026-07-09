<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->removeDuplicates();

        Schema::table('website_paypal_transactions', function (Blueprint $table) {
            $table->unique('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('website_paypal_transactions', function (Blueprint $table) {
            $table->dropUnique(['transaction_id']);
        });
    }

    private function removeDuplicates(): void
    {
        DB::table('website_paypal_transactions')
            ->select('transaction_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('transaction_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->each(function ($row) {
                DB::table('website_paypal_transactions')
                    ->where('transaction_id', $row->transaction_id)
                    ->where('id', '!=', $row->keep_id)
                    ->delete();
            });
    }
};
