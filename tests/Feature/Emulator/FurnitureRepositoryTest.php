<?php

use App\Emulator\Drivers\Arcturus\ArcturusFurnitureRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    installHotel();
});

test('arcturus grants inventory rows referencing item_id', function () {
    $user = User::factory()->create();

    (new ArcturusFurnitureRepository)->grant($user, 230, 3);

    expect(DB::table('items')->where('user_id', $user->id)->where('item_id', 230)->count())->toBe(3);
});

test('arcturus reads limited editions from its catalog', function () {
    $itemId = (int) DB::table('catalog_items')->where('item_ids', 'not like', '%;%')->value('item_ids');
    DB::table('catalog_items')->where('item_ids', (string) $itemId)->update(['limited_stack' => 10]);

    expect((new ArcturusFurnitureRepository)->isLimitedEdition($itemId))->toBeTrue()
        ->and((new ArcturusFurnitureRepository)->definitionCount())->toBeGreaterThan(0);
});

test('arcturus grants a large quantity in bounded chunks', function () {
    $user = User::factory()->create();
    $amount = 1200;

    $inserts = 0;
    DB::listen(function ($query) use (&$inserts): void {
        if (str_starts_with(strtolower(ltrim($query->sql)), 'insert into `items`')) {
            $inserts++;
        }
    });

    (new ArcturusFurnitureRepository)->grant($user, 230, $amount);

    expect(DB::table('items')->where('user_id', $user->id)->count())->toBe($amount)
        // A row per query would be 1200 statements; chunking keeps it at
        // ceil(1200 / 500) so a big package cannot exceed max_allowed_packet.
        ->and($inserts)->toBe(3);
});

test('arcturus ignores a grant of nothing', function () {
    $user = User::factory()->create();

    (new ArcturusFurnitureRepository)->grant($user, 230, 0);
    (new ArcturusFurnitureRepository)->grant($user, 230, -5);

    expect(DB::table('items')->where('user_id', $user->id)->count())->toBe(0);
});
