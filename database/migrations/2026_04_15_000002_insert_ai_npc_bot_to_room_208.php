<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Inserts the AI NPC bot into the Arcturus 'bots' table for room 208.
     * The bot is placed at the center of the room.
     *
     * Note: The Arcturus bots table often uses latin1 charset which cannot
     * store Turkish/Unicode characters. We convert the relevant columns
     * to utf8mb4 before inserting.
     */
    public function up(): void
    {
        // Only insert if the bots table exists (Arcturus emulator table)
        if (!Schema::hasTable('bots')) {
            return;
        }

        // Convert bots table columns to utf8mb4 so Turkish characters work
        DB::statement('ALTER TABLE `bots` MODIFY `name` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE `bots` MODIFY `motto` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE `bots` MODIFY `chat_lines` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        DB::table('bots')->insert([
            'user_id' => 1, // Admin user
            'room_id' => config('npc.bot.room_id', 208),
            'name' => config('npc.bot.name', 'Atlas'),
            'motto' => config('npc.bot.motto', 'Merhaba! Benimle sohbet et.'),
            'figure' => config('npc.bot.figure', 'hr-3163-45.hd-180-1.ch-3030-65.lg-275-76.sh-3016-92.ha-1004-1408'),
            'gender' => config('npc.bot.gender', 'M'),
            'x' => config('npc.bot.x', 13),
            'y' => config('npc.bot.y', 13),
            'z' => config('npc.bot.z', 0.0),
            'chat_lines' => "Merhaba! Yanıma gel ve benimle sohbet et.\rNasıl yardımcı olabilirim?\rBuradayım, merak ettiğin bir şey var mı?",
            'chat_auto' => '1',
            'chat_random' => '1',
            'chat_delay' => 30,
            'dance' => 0,
            'freeroam' => '0',
            'type' => 'generic',
            'effect' => 0,
            'bubble_id' => 31,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('bots')) {
            return;
        }

        DB::table('bots')
            ->where('name', config('npc.bot.name', 'Atlas'))
            ->where('room_id', config('npc.bot.room_id', 208))
            ->delete();
    }
};
