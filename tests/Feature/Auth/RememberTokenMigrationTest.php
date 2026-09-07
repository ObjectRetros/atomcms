<?php

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('the remember-token migration preserves an existing hotel token column', function () {
    $user = User::factory()->create();
    $migration = require database_path('migrations/2026_09_07_204053_add_website_remember_token_to_users_table.php');

    Schema::table('users', function (Blueprint $table): void {
        $table->rememberToken();
    });

    try {
        DB::table('users')->where('id', $user->id)->update(['remember_token' => 'existing-hotel-token']);
        $migration->down();
        $migration->up();

        expect($user->fresh()->getRememberToken())->toBe('existing-hotel-token')
            ->and(DB::table('users')->where('id', $user->id)->value('remember_token'))->toBe('existing-hotel-token');

        $user->refresh()->setRememberToken('rotated-cms-token');
        $user->save();
        $migration->up();

        expect($user->fresh()->getRememberToken())->toBe('rotated-cms-token');
    } finally {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('remember_token');
        });
    }
});
