<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Stevebauman\Purify\Facades\Purify;

return new class extends Migration
{
    public function up(): void
    {
        $this->sanitizeColumn('website_articles', 'full_story');
        $this->sanitizeColumn('website_help_center_categories', 'content');
        $this->sanitizeColumn('website_help_center_tickets', 'content');
        $this->sanitizeColumn('website_help_center_ticket_replies', 'content');
    }

    public function down(): void
    {
        // Sanitization is intentionally irreversible; unsafe markup is discarded.
    }

    private function sanitizeColumn(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        DB::table($table)
            ->select(['id', $column])
            ->whereNotNull($column)
            ->chunkById(100, function ($rows) use ($table, $column): void {
                foreach ($rows as $row) {
                    $value = (string) $row->{$column};
                    $sanitized = Purify::clean($value);

                    if ($sanitized !== $value) {
                        DB::table($table)
                            ->where('id', $row->id)
                            ->update([$column => $sanitized]);
                    }
                }
            });
    }
};
