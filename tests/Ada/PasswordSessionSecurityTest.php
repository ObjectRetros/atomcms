<?php

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

test('ada remembered logins persist in CMS storage and are revoked after a password reset', function () {
    installHotel();
    $user = User::factory()->create();
    $cookieName = Auth::guard()->getRecallerName();

    $login = $this->post(route('login.store'), [
        'username' => $user->username,
        'password' => 'password',
        'remember' => true,
    ])->assertRedirect(route('me.show'))->assertCookie($cookieName);

    $rememberedCookie = $login->getCookie($cookieName)->getValue();
    expect($user->fresh()->getRememberToken())->not->toBeEmpty();

    session()->flush();
    Auth::forgetGuards();
    $this->withCookie($cookieName, $rememberedCookie)->get(route('me.show'))->assertOk();
    $this->assertAuthenticatedAs($user);

    $oldSession = session()->all();
    session()->flush();
    Auth::forgetGuards();
    $this->withCookies([$cookieName => '']);
    PasswordResetToken::create([
        'email' => $user->mail,
        'token' => PasswordResetToken::hashToken('ada-reset-token'),
    ]);

    $this->post(route('reset.password.post', 'ada-reset-token'), [
        'password' => 'ChangedPassword123!',
        'password_confirmation' => 'ChangedPassword123!',
    ])->assertRedirect(route('login'));

    expect(Hash::check('ChangedPassword123!', DB::table('players')->where('id', $user->id)->value('password')))->toBeTrue();

    Auth::forgetGuards();
    $this->withSession($oldSession)->get(route('me.show'))->assertRedirect(route('login'));
    $this->assertGuest();

    Auth::forgetGuards();
    $this->withCookie($cookieName, $rememberedCookie)->get(route('me.show'))->assertRedirect(route('login'));
    $this->assertGuest();
});
