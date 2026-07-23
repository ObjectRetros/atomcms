<?php

use App\Models\ChatlogRoom;
use App\Models\CommandLog;
use App\Models\User;

beforeEach(function () {
    installHotel();
});

test('the emulator log models expose no primary key', function () {
    expect((new ChatlogRoom)->getKeyName())->toBeNull()
        ->and((new CommandLog)->getKeyName())->toBeNull();
});

test('keyed deletes on log models are refused instead of hitting arbitrary rows', function () {
    $user = User::factory()->create();
    $timestamp = time();

    ChatlogRoom::query()->insert([
        ['room_id' => 1, 'user_from_id' => $user->id, 'user_to_id' => 0, 'message' => 'first', 'timestamp' => $timestamp],
        ['room_id' => 1, 'user_from_id' => $user->id, 'user_to_id' => 0, 'message' => 'second', 'timestamp' => $timestamp],
    ]);

    $row = ChatlogRoom::query()->where('message', 'first')->firstOrFail();

    expect(fn () => $row->delete())->toThrow(LogicException::class)
        ->and(ChatlogRoom::query()->count())->toBe(2);
});
