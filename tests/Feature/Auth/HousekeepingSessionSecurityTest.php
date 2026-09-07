<?php

use App\Filament\Pages\Dashboard;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    installHotel();
    setSetting('force_staff_2fa', '0');
    grantHousekeepingPermission('can_access_housekeeping', 6);

    $this->staff = User::factory()->create(['rank' => 6]);
});

test('a pre-upgrade staff session cannot enter housekeeping after a password reset', function () {
    $legacySession = [Auth::guard()->getName() => $this->staff->id];

    PasswordResetToken::create([
        'email' => $this->staff->mail,
        'token' => PasswordResetToken::hashToken('legacy-staff-reset-token'),
    ]);

    $this->post(route('reset.password.post', 'legacy-staff-reset-token'), [
        'password' => 'ChangedPassword123!',
        'password_confirmation' => 'ChangedPassword123!',
    ])->assertRedirect(route('login'));

    Auth::forgetGuards();
    $this->withSession($legacySession)
        ->get('/housekeeping')
        ->assertRedirect(route('filament.housekeeping.auth.login'));
    $this->assertGuest();
});

test('a fresh website login grants an eligible staff member housekeeping access', function () {
    $this->post(route('login.store'), [
        'username' => $this->staff->username,
        'password' => 'password',
    ])->assertRedirect(route('me.show'));

    Auth::forgetGuards();
    $this->get('/housekeeping')->assertOk();
    $this->assertAuthenticatedAs($this->staff);
});

test('an unchanged password allows remembered housekeeping authentication', function () {
    $this->staff->setRememberToken(Str::random(60));
    $this->staff->save();
    $guard = Auth::guard();
    $cookie = $this->staff->id . '|' . $this->staff->getRememberToken() . '|' . $guard->hashPasswordForCookie($this->staff->password);

    $this->withCookie($guard->getRecallerName(), $cookie)->get('/housekeeping')->assertOk();
    $this->assertAuthenticatedAs($this->staff);
});

test('housekeeping livewire requests reject a session after its password changes', function () {
    $page = $this->actingAs($this->staff)->get('/housekeeping')->assertOk();
    $oldSession = session()->all();
    $oldSession[Auth::guard()->getName()] = $this->staff->id;
    preg_match_all('/wire:snapshot="([^"]+)"/', $page->getContent(), $matches);
    $component = Livewire::new(Dashboard::class)->getName();
    $snapshot = collect($matches[1])
        ->map(fn (string $value) => html_entity_decode($value, ENT_QUOTES | ENT_HTML5))
        ->first(fn (string $value) => json_decode($value, true, flags: JSON_THROW_ON_ERROR)['memo']['name'] === $component);

    expect($snapshot)->toBeString();
    $this->staff->changePassword('ChangedPassword123!');
    Auth::forgetGuards();

    $this->withSession($oldSession)->postJson(Livewire::getUpdateUri(), [
        'components' => [[
            'snapshot' => $snapshot,
            'updates' => [],
            'calls' => [['method' => '$refresh', 'params' => []]],
        ]],
    ], ['X-Livewire' => 'true'])->assertUnauthorized();

    $this->assertGuest();
});
