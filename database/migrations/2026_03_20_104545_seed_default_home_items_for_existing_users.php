<?php

use App\Enums\HomeItemType;
use App\Models\Home\HomeItem;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $background = HomeItem::where('type', HomeItemType::Background)->orderBy('id')->first();
        $widget = HomeItem::where('type', HomeItemType::Widget)->where('name', 'My Profile')->first();

        if (! $background && ! $widget) {
            return;
        }

        $usersWithoutHomes = User::whereNotIn('id', function ($query) {
            $query->select('user_id')->from('user_home_items');
        })->pluck('id');

        if ($usersWithoutHomes->isEmpty()) {
            return;
        }

        $rows = [];
        $now = now();

        foreach ($usersWithoutHomes as $userId) {
            if ($background) {
                $rows[] = [
                    'user_id' => $userId,
                    'home_item_id' => $background->id,
                    'x' => 0,
                    'y' => 0,
                    'z' => 0,
                    'placed' => true,
                    'is_reversed' => false,
                    'theme' => null,
                    'extra_data' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($widget) {
                $rows[] = [
                    'user_id' => $userId,
                    'home_item_id' => $widget->id,
                    'x' => 300,
                    'y' => 100,
                    'z' => 1,
                    'placed' => true,
                    'is_reversed' => false,
                    'theme' => 'default',
                    'extra_data' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('user_home_items')->insert($chunk);
        }
    }

    public function down(): void {}
};
