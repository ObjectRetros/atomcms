<?php

use App\Emulator\Contracts\BadgeRepository;
use App\Filament\Resources\User\Users\Pages\EditUser;
use App\Filament\Resources\User\Users\RelationManagers\BadgesRelationManager;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    installHotel();
    grantHousekeepingPermission('can_access_housekeeping', 6);
    grantHousekeepingPermission('edit_user', 6);
    $this->actingAs(User::factory()->create(['rank' => 7]));
    Filament::setCurrentPanel(Filament::getPanel('housekeeping'));
});

test('housekeeping grants offline badges once and preserves equipped ownership', function () {
    $user = User::factory()->create();
    $badges = app(BadgeRepository::class);
    $manager = Livewire::test(BadgesRelationManager::class, [
        'ownerRecord' => $user,
        'pageClass' => EditUser::class,
    ]);

    $manager->callAction(TestAction::make(CreateAction::class)->table(), data: ['badge_code' => 'ADMIN_ONCE'])
        ->assertHasNoActionErrors();

    expect($badges->codes($user))->toBe(['ADMIN_ONCE']);

    $badges->relation($user)->update(['slot_id' => 3]);

    $manager->callAction(TestAction::make(CreateAction::class)->table(), data: ['badge_code' => 'ADMIN_ONCE'])
        ->assertHasNoActionErrors();

    expect($badges->codes($user))->toBe(['ADMIN_ONCE'])
        ->and($badges->relation($user)->value('slot_id'))->toBe(3)
        ->and($this->rcon->calls)->toBe([]);
});

test('housekeeping sends online badges only through connected RCON', function (bool $connected) {
    $user = User::factory()->create(['online' => '1'])->refresh();
    $this->rcon->connected($connected);

    Livewire::test(BadgesRelationManager::class, [
        'ownerRecord' => $user,
        'pageClass' => EditUser::class,
    ])->callAction(TestAction::make(CreateAction::class)->table(), data: ['badge_code' => 'ADMIN_ONLINE'])
        ->assertHasNoActionErrors()
        ->assertNotified($connected ? 'Badge sent through the emulator' : 'RCON is not connected!');

    expect(app(BadgeRepository::class)->codes($user))->toBe([])
        ->and(array_column($this->rcon->calls, 'method'))->toBe($connected ? ['giveBadge'] : []);
})->with([true, false]);
