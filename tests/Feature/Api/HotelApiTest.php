<?php

use App\Models\User;

beforeEach(function () {
    installHotel();
});

test('a user can be fetched by username', function () {
    $user = User::factory()->create([
        'motto' => 'Hello from Atom',
        'mail' => 'private@example.com',
        'ip_register' => '203.0.113.10',
        'auth_ticket' => 'private-ticket',
    ]);

    $this->getJson('/api/user/' . $user->username)
        ->assertOk()
        ->assertExactJson([
            'data' => [
                'username' => $user->username,
                'motto' => $user->motto,
                'look' => $user->look,
            ],
        ]);
});

test('an unknown API user returns a JSON 404', function () {
    $this->getJson('/api/user/DoesNotExist')
        ->assertNotFound()
        ->assertExactJson(['message' => 'User not found.']);
});

test('users can be searched by prefix', function () {
    $target = User::factory()->create(['username' => 'SearchTargetOne']);
    User::factory()->create(['username' => 'SomebodyElse']);

    $this->getJson('/api/users/search?q=SearchTarget')
        ->assertOk()
        ->assertExactJson([
            ['username' => 'SearchTargetOne', 'look' => $target->look],
        ]);
});

test('short search queries return nothing', function () {
    $this->getJson('/api/users/search?q=a')
        ->assertOk()
        ->assertExactJson([]);
});

test('online users and their count are reported', function () {
    $online = User::factory()->create([
        'online' => '1',
        'motto' => 'Online now',
        'mail' => 'private@example.com',
        'auth_ticket' => 'private-ticket',
    ]);
    User::factory()->create(['online' => '0']);

    $this->getJson('/api/online-count')
        ->assertOk()
        ->assertJsonPath('data.onlineCount', 1);

    $this->getJson('/api/online-users')
        ->assertOk()
        ->assertExactJson([
            'data' => [[
                'username' => $online->username,
                'motto' => $online->motto,
                'look' => $online->look,
            ]],
        ]);
});
