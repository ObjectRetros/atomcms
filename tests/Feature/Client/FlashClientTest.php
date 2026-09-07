<?php

use App\Models\User;

test('the flash client page renders and refreshes the session ticket', function () {
    installHotel();

    $user = User::factory()->create(['auth_ticket' => '']);

    $this->actingAs($user)
        ->get(route('flash-client'))
        ->assertOk()
        ->assertSee('assets/js/flashclient.js', false)
        ->assertSee('assets/js/swfobject.js', false)
        ->assertDontSee('assets/js/jquery-latest.js', false)
        ->assertDontSee('assets/js/jquery-ui.js', false);

    expect($user->fresh()->auth_ticket)->not->toBe('');
});

test('flash configuration is escaped for JavaScript instead of HTML', function () {
    installHotel();

    config([
        'habbo.flash.external_texts' => 'texts.txt?lang=en&name="hotel"',
        'habbo.flash.habbo_swf' => "hotel's\\client.swf",
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('flash-client'))
        ->assertOk()
        ->assertSee('texts.txt?lang=en\\u0026name=\\u0022hotel\\u0022', false)
        ->assertSee('hotel\\u0027s\\\\client.swf', false)
        ->assertDontSee('&amp;name=', false)
        ->assertDontSee('hotel&#039;s', false);
});
