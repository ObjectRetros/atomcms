<?php

use App\Models\User;

test('guests are redirected away from the housekeeping panel', function () {
    installHotel();

    $this->get('/housekeeping')->assertRedirect();
});

test('users without the housekeeping permission are forbidden', function () {
    installHotel();

    $user = User::factory()->create();

    $this->actingAs($user)->get('/housekeeping')->assertForbidden();
});
