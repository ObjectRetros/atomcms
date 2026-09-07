<?php

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

beforeEach(function () {
    installHotel();
    $this->user = User::factory()->create();
});

test('resetting a password rejects an existing authenticated session', function () {
    $this->actingAs($this->user)->get(route('me.show'))->assertOk();
    $oldSession = session()->all();
    $oldSession[Auth::guard()->getName()] = $this->user->id;

    session()->flush();
    Auth::forgetGuards();
    PasswordResetToken::create([
        'email' => $this->user->mail,
        'token' => PasswordResetToken::hashToken('reset-session-token'),
    ]);

    $this->post(route('reset.password.post', 'reset-session-token'), [
        'password' => 'ChangedPassword123!',
        'password_confirmation' => 'ChangedPassword123!',
    ])->assertRedirect(route('login'));

    Auth::forgetGuards();
    $this->withSession($oldSession)->get(route('me.show'))->assertRedirect(route('login'));
    $this->assertGuest();
});

test('resetting a password revokes the previous remember-me cookie', function () {
    $this->user->setRememberToken(Str::random(60));
    $this->user->save();
    $guard = Auth::guard();
    $cookieName = $guard->getRecallerName();
    $cookie = $this->user->id . '|' . $this->user->getRememberToken() . '|' . $guard->hashPasswordForCookie($this->user->password);

    PasswordResetToken::create([
        'email' => $this->user->mail,
        'token' => PasswordResetToken::hashToken('reset-cookie-token'),
    ]);

    $this->post(route('reset.password.post', 'reset-cookie-token'), [
        'password' => 'ChangedPassword123!',
        'password_confirmation' => 'ChangedPassword123!',
    ])->assertRedirect(route('login'));

    Auth::forgetGuards();
    $this->withCookie($cookieName, $cookie)->get(route('me.show'))->assertRedirect(route('login'));
    $this->assertGuest();
});

test('an unchanged password still allows remembered authentication', function () {
    $this->user->setRememberToken(Str::random(60));
    $this->user->save();
    $guard = Auth::guard();
    $cookie = $this->user->id . '|' . $this->user->getRememberToken() . '|' . $guard->hashPasswordForCookie($this->user->password);

    $this->withCookie($guard->getRecallerName(), $cookie)->get(route('me.show'))->assertOk();
    $this->assertAuthenticatedAs($this->user);
    expect($this->user->toArray())->not->toHaveKey('website_remember_token');
});

test('changing a password preserves the current session and revokes remembered logins', function () {
    $oldRememberToken = $this->user->getRememberToken();

    $this->actingAs($this->user)->get(route('settings.password.show'))->assertOk();
    $oldSession = session()->all();
    $oldSession[Auth::guard()->getName()] = $this->user->id;
    $this->put(route('settings.password.update'), [
        'current_password' => 'password',
        'password' => 'ChangedPassword123!',
        'password_confirmation' => 'ChangedPassword123!',
    ])->assertRedirect(route('settings.password.show'));

    $this->get(route('me.show'))->assertOk();
    $this->assertAuthenticatedAs($this->user);
    expect($this->user->fresh()->getRememberToken())->not->toBe($oldRememberToken);

    Auth::forgetGuards();
    $this->withSession($oldSession)->get(route('me.show'))->assertRedirect(route('login'));
    $this->assertGuest();
});
