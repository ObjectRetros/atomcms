<?php

use App\Models\Miscellaneous\WebsitePermission;
use App\Models\User;
use App\Models\WebsiteHousekeepingPermission;
use App\Policies\BanPolicy;
use App\Policies\WebsiteHelpCenterTicketPolicy;

test('guests are redirected away from the housekeeping panel', function () {
    installHotel();

    $this->get('/housekeeping')->assertRedirect();
});

test('users without the housekeeping permission are forbidden', function () {
    installHotel();

    $user = User::factory()->create();

    $this->actingAs($user)->get('/housekeeping')->assertForbidden();
});

test('policies evaluate the explicit actor instead of global authentication', function () {
    installHotel();

    WebsiteHousekeepingPermission::query()->updateOrCreate(
        ['permission' => 'manage_bans'],
        ['min_rank' => 5, 'description' => 'Manage bans'],
    );
    WebsitePermission::query()->updateOrCreate(
        ['permission' => 'manage_website_tickets'],
        ['min_rank' => 5, 'description' => 'Manage tickets'],
    );

    $signedInUser = User::factory()->create(['rank' => 1]);
    $explicitActor = User::factory()->create(['rank' => 7]);
    $this->actingAs($signedInUser);

    $banPolicy = app(BanPolicy::class);
    $ticketPolicy = app(WebsiteHelpCenterTicketPolicy::class);

    expect($banPolicy->viewAny($explicitActor))->toBeTrue()
        ->and($banPolicy->viewAny($signedInUser))->toBeFalse()
        ->and($ticketPolicy->viewAny($explicitActor))->toBeTrue()
        ->and($ticketPolicy->viewAny($signedInUser))->toBeFalse();
});
