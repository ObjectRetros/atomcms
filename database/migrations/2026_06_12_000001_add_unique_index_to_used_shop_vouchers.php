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

        Schema::table('website_used_shop_vouchers', function (Blueprint $table) {
            $table->unique(['user_id', 'voucher_id']);
        });
    }

    public function down(): void
    {
        Schema::table('website_used_shop_vouchers', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'voucher_id']);
        });
    }

    /**
     * Drop any duplicate redemptions left behind by the previous race so the
     * unique index can be created, keeping the earliest row per pair.
     */
    private function removeDuplicates(): void
    {
        DB::table('website_used_shop_vouchers')
            ->select('user_id', 'voucher_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('user_id', 'voucher_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->each(function ($row) {
                DB::table('website_used_shop_vouchers')
                    ->where('user_id', $row->user_id)
                    ->where('voucher_id', $row->voucher_id)
                    ->where('id', '!=', $row->keep_id)
                    ->delete();
            });
    }
};
