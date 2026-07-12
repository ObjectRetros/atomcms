<?php

use App\Emulator\Drivers\Arcturus\ArcturusFurnitureRepository;
use App\Emulator\Drivers\Plus\PlusFurnitureRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    installHotel();
});

afterEach(function () {
    // The Plus test shadows the real items table for this connection.
    DB::statement('DROP TEMPORARY TABLE IF EXISTS items');
});

test('arcturus grants inventory rows referencing item_id', function () {
    $user = User::factory()->create();

    (new ArcturusFurnitureRepository)->grant($user, 230, 3);

    expect($user->items()->where('item_id', 230)->count())->toBe(3);
});

test('plus grants inventory rows referencing base_item', function () {
    DB::statement(<<<'SQL'
        CREATE TEMPORARY TABLE items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            base_item INT NOT NULL,
            room_id INT NOT NULL DEFAULT 0,
            extra_data VARCHAR(255) NOT NULL DEFAULT ''
        )
    SQL);

    $user = User::factory()->create();

    (new PlusFurnitureRepository)->grant($user, 230, 3);

    expect(DB::table('items')->where('user_id', $user->id)->where('base_item', 230)->count())->toBe(3);
});
