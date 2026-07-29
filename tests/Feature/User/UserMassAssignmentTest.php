<?php

use App\Filament\Resources\User\Users\Pages\EditUser;
use App\Models\User;
use App\Models\WebsiteHousekeepingPermission;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    installHotel();
});

test('privileged columns cannot be mass assigned', function () {
    $user = User::factory()->create([
        'rank' => 1,
        'credits' => 100,
        'hidden_staff' => false,
        'auth_ticket' => '',
    ]);

    $user->update([
        'rank' => 7,
        'credits' => 999999,
        'hidden_staff' => true,
        'extra_rank' => 7,
        'auth_ticket' => 'hijacked-ticket',
        'team_id' => 1,
        'motto' => 'Updated motto',
    ]);

    $user->refresh();

    expect((int) $user->rank)->toBe(1)
        ->and((int) $user->credits)->toBe(100)
        ->and($user->hidden_staff)->toBeFalse()
        ->and($user->extra_rank)->toBeNull()
        ->and($user->auth_ticket)->toBe('')
        ->and($user->team_id)->toBeNull()
        ->and($user->motto)->toBe('Updated motto');
});

test('the housekeeping user editor still saves privileged fields', function () {
    WebsiteHousekeepingPermission::query()->create([
        'permission' => 'can_access_housekeeping',
        'min_rank' => 5,
        'description' => 'Access housekeeping',
    ]);
    WebsiteHousekeepingPermission::query()->create([
        'permission' => 'edit_user',
        'min_rank' => 5,
        'description' => 'Edit users',
    ]);

    $actor = User::factory()->create(['rank' => 7]);
    $target = User::factory()->create([
        'rank' => 1,
        'hidden_staff' => false,
    ]);

    $this->actingAs($actor);
    Filament::setCurrentPanel(Filament::getPanel('housekeeping'));

    Livewire::test(EditUser::class, ['record' => $target->getRouteKey()])
        ->fillForm([
            'motto' => 'Promoted by housekeeping',
            'mail' => 'target@example.com',
            'rank' => 5,
            'hidden_staff' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $target->refresh();

    expect((int) $target->rank)->toBe(5)
        ->and($target->hidden_staff)->toBeTrue();
});
