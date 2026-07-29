<?php

use App\Emulator\Contracts\PlayerRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    installHotel();
});

/**
 * rank, credits, auth_ticket, extra_rank, team_id and hidden_staff are no
 * longer mass assignable. Every trusted path that used to set them through
 * create()/update() has to keep working - a silent no-op here would surface
 * only as players registering with no money.
 */
test('the guarded columns really are out of the fillable list', function () {
    $fillable = (new User)->getFillable();

    foreach (['rank', 'credits', 'auth_ticket', 'extra_rank', 'team_id', 'hidden_staff'] as $column) {
        expect($fillable)->not->toContain($column);
    }
});

test('registration still persists the guarded starting balance', function () {
    setSetting('start_credits', '4321');

    $this->post('/register', [
        'username' => 'GuardedNewbie',
        'mail' => 'guarded@example.com',
        'password' => 'Sup3rSecret!',
        'password_confirmation' => 'Sup3rSecret!',
        'terms' => 'on',
    ])->assertRedirect();

    $user = User::where('username', 'GuardedNewbie')->firstOrFail();

    expect((int) $user->credits)->toBe(4321)
        ->and($user->referral_code)->not->toBeEmpty();
});

test('issuing an sso ticket still writes the guarded auth ticket', function () {
    $user = User::factory()->create();

    $ticket = app(PlayerRepository::class)->issueSso($user);

    // Assert it persisted, not merely that a string came back - a guarded
    // column turns the write into a silent no-op.
    expect($ticket)->not->toBeEmpty()
        ->and($user->fresh()->auth_ticket)->toBe($ticket);
});

test('online friends come back most recently seen first', function () {
    $user = User::factory()->create();
    $stale = User::factory()->create(['online' => '1', 'last_online' => 1_000]);
    $recent = User::factory()->create(['online' => '1', 'last_online' => 9_000]);

    foreach ([$stale, $recent] as $friend) {
        DB::table('messenger_friendships')->insert([
            'user_one_id' => $user->id,
            'user_two_id' => $friend->id,
            'relation' => 0,
            'friends_since' => time(),
            'category' => 0,
        ]);
    }

    expect(app(PlayerRepository::class)->onlineFriends($user, 10)->pluck('id')->all())
        ->toBe([$recent->id, $stale->id]);
});
