<?php

use App\Filament\Pages\Login;
use App\Filament\Pages\TwoFactorAuthentication;
use App\Models\User;
use Filament\Auth\MultiFactor\MultiFactorChallenge;
use Filament\Facades\Filament;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Fortify;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

beforeEach(function () {
    installHotel();
    grantHousekeepingPermission('can_access_housekeeping', 6);
    setSetting('force_staff_2fa', '0');
    Filament::setCurrentPanel(Filament::getPanel('housekeeping'));
    $this->staff = User::factory()->create(['rank' => 6]);
    app(EnableTwoFactorAuthentication::class)($this->staff);
    $this->staff->forceFill(['two_factor_confirmed_at' => now()])->save();
});

test('housekeeping accepts a Fortify authenticator code and establishes the remembered session only afterwards', function () {
    $login = Livewire::test(Login::class)
        ->set('data.username', $this->staff->username)
        ->set('data.password', 'password')
        ->set('data.remember', true)
        ->call('authenticate');

    $sessionId = session()->getId();
    $secret = Fortify::currentEncrypter()->decrypt($this->staff->two_factor_secret);
    $code = app(Google2FA::class)->getCurrentOtp($secret);

    $login->set('data.multiFactor.fortify.code', $code)
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect('/housekeeping');

    $this->assertAuthenticatedAs($this->staff);
    expect($this->staff->fresh()->getRememberToken())->not->toBeNull()
        ->and(session()->getId())->not->toBe($sessionId)
        ->and(session()->has('password_hash_web'))->toBeTrue();

    $this->post(route('filament.housekeeping.auth.logout'))->assertRedirect();
    $this->assertGuest();
    expect(session()->has('password_hash_web'))->toBeFalse();
});

test('missing and invalid factors leave housekeeping guests unauthenticated', function (string $code) {
    Livewire::test(Login::class)
        ->set('data.username', $this->staff->username)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->set('data.multiFactor.fortify.code', $code)
        ->call('authenticate')
        ->assertHasErrors('data.multiFactor.fortify.code');

    $this->assertGuest();
})->with(['missing' => '', 'invalid' => 'invalid-recovery-code']);

test('invalid passwords do not start the housekeeping factor challenge', function () {
    Livewire::test(Login::class)
        ->set('data.username', $this->staff->username)
        ->set('data.password', 'incorrect')
        ->call('authenticate')
        ->assertHasErrors('data.username')
        ->assertSet('userUndertakingMultiFactorAuthentication', null);

    $this->assertGuest();
});

test('housekeeping preserves login for users without confirmed two-factor authentication', function (bool $pending) {
    $this->staff->forceFill([
        'two_factor_confirmed_at' => null,
        'two_factor_secret' => $pending ? $this->staff->two_factor_secret : null,
    ])->save();

    Livewire::test(Login::class)
        ->set('data.username', $this->staff->username)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasNoErrors();

    $this->assertAuthenticatedAs($this->staff);
})->with(['unenrolled' => false, 'pending confirmation' => true]);

test('a recovery code consumed by housekeeping cannot be reused through Fortify', function () {
    $recoveryCode = $this->staff->recoveryCodes()[0];

    Livewire::test(Login::class)
        ->set('data.username', $this->staff->username)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->set('data.multiFactor.fortify.code', $recoveryCode)
        ->call('authenticate')
        ->assertHasNoErrors();

    $this->assertAuthenticatedAs($this->staff);
    expect($this->staff->fresh()->recoveryCodes())->not->toContain($recoveryCode);
    auth()->logout();

    $this->post(route('login.store'), ['username' => $this->staff->username, 'password' => 'password'])
        ->assertRedirect(route('two-factor.login'));
    $this->post('/two-factor-challenge', ['recovery_code' => $recoveryCode])->assertSessionHasErrors('code');
    $this->assertGuest();
    $this->post('/two-factor-challenge', ['recovery_code' => $this->staff->fresh()->recoveryCodes()[0]])
        ->assertSessionMissing('login.id');
    $this->assertAuthenticatedAs($this->staff);
});

test('a recovery code consumed by Fortify cannot be reused through housekeeping', function () {
    $recoveryCode = $this->staff->recoveryCodes()[0];
    $this->post(route('login.store'), ['username' => $this->staff->username, 'password' => 'password']);
    $this->post('/two-factor-challenge', ['recovery_code' => $recoveryCode]);
    $this->assertAuthenticatedAs($this->staff);
    auth()->logout();

    Livewire::test(Login::class)
        ->set('data.username', $this->staff->username)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->set('data.multiFactor.fortify.code', $recoveryCode)
        ->call('authenticate')
        ->assertHasErrors('data.multiFactor.fortify.code');

    $this->assertGuest();
});

test('housekeeping and Fortify share authenticator code replay protection', function () {
    $secret = Fortify::currentEncrypter()->decrypt($this->staff->two_factor_secret);
    $code = app(Google2FA::class)->getCurrentOtp($secret);

    Livewire::test(Login::class)
        ->set('data.username', $this->staff->username)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->set('data.multiFactor.fortify.code', $code)
        ->call('authenticate')->assertHasNoErrors();
    $this->assertAuthenticatedAs($this->staff);
    auth()->logout();

    $this->post(route('login.store'), ['username' => $this->staff->username, 'password' => 'password']);
    $this->post('/two-factor-challenge', ['code' => $code])->assertSessionHasErrors('code');
    $this->assertGuest();
});

test('a throttled housekeeping challenge does not consume a valid recovery code', function () {
    $login = Livewire::test(Login::class)
        ->set('data.username', $this->staff->username)
        ->set('data.password', 'password')
        ->call('authenticate');
    $recoveryCode = $this->staff->recoveryCodes()[0];

    foreach (range(1, 5) as $attempt) {
        MultiFactorChallenge::make()->hitRateLimiter($this->staff);
    }

    $login->set('data.multiFactor.fortify.code', $recoveryCode)->call('authenticate');
    $this->assertGuest();
    expect($this->staff->fresh()->recoveryCodes())->toContain($recoveryCode);
});

test('changing the challenged account does not authenticate with the first accounts factor', function () {
    $other = User::factory()->create(['rank' => 6]);
    app(EnableTwoFactorAuthentication::class)($other);
    $other->forceFill(['two_factor_confirmed_at' => now()])->save();

    $login = Livewire::test(Login::class)
        ->set('data.username', $this->staff->username)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->set('data.username', $other->username)
        ->set('data.multiFactor.fortify.code', $this->staff->recoveryCodes()[0])
        ->call('authenticate');

    $this->assertGuest();
    $login->set('data.multiFactor.fortify.code', $this->staff->recoveryCodes()[0])
        ->call('authenticate')
        ->assertHasErrors('data.multiFactor.fortify.code');
    $this->assertGuest();
});

test('housekeeping rechecks the password and panel access before accepting the factor', function (string $change) {
    $login = Livewire::test(Login::class)
        ->set('data.username', $this->staff->username)
        ->set('data.password', 'password')
        ->call('authenticate');

    $this->staff->forceFill($change === 'password' ? ['password' => 'changed-password'] : ['rank' => 1])->save();

    $login->set('data.multiFactor.fortify.code', $this->staff->recoveryCodes()[0])
        ->call('authenticate')
        ->assertHasErrors('data.username');
    $this->assertGuest();
})->with(['password', 'permission']);

test('housekeeping two-factor setup uses the existing Fortify enrollment and requires the current password', function () {
    $this->staff->forceFill([
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
        'two_factor_confirmed_at' => null,
    ])->save();
    $this->actingAs($this->staff);
    setSetting('force_staff_2fa', '1');

    $this->get('/housekeeping')->assertRedirect(TwoFactorAuthentication::getUrl());
    $this->get(TwoFactorAuthentication::getUrl())->assertOk();

    $page = Livewire::test(TwoFactorAuthentication::class)
        ->set('currentPassword', 'incorrect')
        ->call('enable')
        ->assertHasErrors('currentPassword');
    expect($this->staff->fresh()->two_factor_secret)->toBeNull();

    $page->set('currentPassword', 'password')->call('enable')->assertHasNoErrors()->assertSee('<svg', false);
    expect($this->staff->fresh()->two_factor_confirmed_at)->toBeNull();

    $page->set('code', 'bad')->call('confirm')->assertHasErrors('code');
    $secret = Fortify::currentEncrypter()->decrypt($this->staff->fresh()->two_factor_secret);
    $page->set('code', app(Google2FA::class)->getCurrentOtp($secret))
        ->call('confirm')->assertHasNoErrors()
        ->assertSee($this->staff->fresh()->recoveryCodes()[0]);
    expect($this->staff->fresh()->hasEnabledTwoFactorAuthentication())->toBeTrue();

    $page->set('currentPassword', 'incorrect')->call('disable')->assertHasErrors('currentPassword');
    expect($this->staff->fresh()->hasEnabledTwoFactorAuthentication())->toBeTrue();
    $page->set('currentPassword', 'password')->call('disable')->assertHasNoErrors();
    expect($this->staff->fresh()->two_factor_secret)->toBeNull();
});

test('housekeeping requires a second factor before authenticating enrolled staff', function () {
    $rememberToken = $this->staff->getRememberToken();

    Livewire::test(Login::class)
        ->set('data.username', $this->staff->username)
        ->set('data.password', 'password')
        ->set('data.remember', true)
        ->call('authenticate')
        ->assertHasNoErrors();

    $this->assertGuest();
    expect($this->staff->fresh()->getRememberToken())->toBe($rememberToken);
});
