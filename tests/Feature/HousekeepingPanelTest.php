<?php

use App\Filament\Resources\Atom\Articles\Pages\CreateArticle;
use App\Models\Articles\WebsiteArticle;
use App\Models\User;
use App\Models\WebsiteHousekeepingPermission;
use App\Services\HousekeepingPermissionsService;
use Livewire\Livewire;

function grantPanelPermission(string $permission, int $minRank): void
{
    WebsiteHousekeepingPermission::query()->updateOrCreate(
        ['permission' => $permission],
        ['min_rank' => $minRank, 'description' => "Testing {$permission}"],
    );

    HousekeepingPermissionsService::clearCache();
}

beforeEach(function () {
    installHotel();
    setSetting('force_staff_2fa', '0');
});

test('eligible staff can open the housekeeping dashboard', function () {
    grantPanelPermission('can_access_housekeeping', 6);

    $staff = User::factory()->create(['rank' => 6]);

    $this->actingAs($staff)
        ->get('/housekeeping')
        ->assertOk();
});

test('the badge page is accessible with the manage badges permission', function () {
    grantPanelPermission('can_access_housekeeping', 6);
    grantPanelPermission('manage_badges', 6);

    $staff = User::factory()->create(['rank' => 6]);

    $this->actingAs($staff)
        ->get('/housekeeping/badge-page')
        ->assertOk();
});

test('the badge page is denied without the manage badges permission', function () {
    grantPanelPermission('can_access_housekeeping', 6);
    grantPanelPermission('manage_badges', 7);

    $staff = User::factory()->create(['rank' => 6]);

    $this->actingAs($staff)
        ->get('/housekeeping/badge-page')
        ->assertForbidden();
});

test('created articles are attributed to the authenticated staff member', function () {
    grantPanelPermission('can_access_housekeeping', 6);
    grantPanelPermission('write_article', 6);

    $staff = User::factory()->create(['rank' => 6]);
    $other = User::factory()->create();

    $this->actingAs($staff);

    Livewire::test(CreateArticle::class)
        ->fillForm([
            'title' => 'Server-side authorship',
            'short_story' => 'The author is never client-controlled',
            'full_story' => '<p>Authored by the signed-in staff member.</p>',
        ])
        // Simulate a tampered Livewire payload trying to attribute the
        // article to another user.
        ->set('data.user_id', $other->id)
        ->call('create')
        ->assertHasNoFormErrors();

    $article = WebsiteArticle::query()->latest('id')->first();

    expect($article)->not->toBeNull()
        ->and($article->user_id)->toBe($staff->id);
});
