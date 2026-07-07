<?php

use App\Models\User;

beforeEach(function () {
    installHotel();
});

test('a user can be fetched by username', function () {
    $user = User::factory()->create();

    $this->getJson('/api/user/' . $user->username)
        ->assertOk()
        ->assertJsonPath('data.username', $user->username);
});

test('users can be searched by prefix', function () {
    User::factory()->create(['username' => 'SearchTargetOne']);
    User::factory()->create(['username' => 'SomebodyElse']);

    $this->getJson('/api/users/search?q=SearchTarget')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.username', 'SearchTargetOne');
});

test('short search queries return nothing', function () {
    $this->getJson('/api/users/search?q=a')
        ->assertOk()
        ->assertExactJson([]);
});

test('online users and their count are reported', function () {
    User::factory()->create(['online' => '1']);
    User::factory()->create(['online' => '0']);

    $this->getJson('/api/online-count')
        ->assertOk()
        ->assertJsonPath('data.onlineCount', 1);

    $this->getJson('/api/online-users')->assertOk();
});
