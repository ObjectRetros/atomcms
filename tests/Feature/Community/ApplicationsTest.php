<?php

use App\Actions\Community\SubmitStaffApplication;
use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\Community\Staff\WebsiteStaffApplications;
use App\Models\Community\Teams\WebsiteTeam;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    installHotel();
    $this->user = User::factory()->create();
});

function openRankPosition(): WebsiteOpenPosition
{
    return WebsiteOpenPosition::create([
        'position_kind' => 'rank',
        'permission_id' => DB::table('permissions')->min('id'),
        'description' => 'Help us moderate the hotel.',
        'apply_from' => now()->subDay(),
        'apply_to' => now()->addDay(),
    ]);
}

function openTeamPosition(): WebsiteOpenPosition
{
    $team = WebsiteTeam::create([
        'rank_name' => 'Events Team',
        'badge' => '',
        'job_description' => 'Host events for the community.',
        'staff_color' => '#ffffff',
        'staff_background' => '',
    ]);

    return WebsiteOpenPosition::create([
        'position_kind' => 'team',
        'team_id' => $team->id,
        'description' => 'Join the events crew.',
        'apply_from' => now()->subDay(),
        'apply_to' => now()->addDay(),
    ]);
}

test('application API preserves rank and team header artwork', function (string $kind) {
    $position = $kind === 'team' ? openTeamPosition() : openRankPosition();
    $role = $kind === 'team' ? $position->team : $position->permission;
    $role->update(['badge' => 'ADM', 'staff_color' => '#123456', 'job_description' => 'Community group description']);

    $this->actingAs($this->user)->getJson('/api/v1/applications/' . $position->id)->assertOk()
        ->assertJsonPath('data.badge', 'ADM')->assertJsonPath('data.color', '#123456')
        ->assertJsonPath('data.group_description', 'Community group description');
    $this->getJson('/api/v1/applications?kind=' . $kind)->assertOk()
        ->assertJsonFragment(['id' => $position->id, 'badge' => 'ADM', 'color' => '#123456']);
})->with(['rank', 'team']);

test('a user can apply for an open staff position once', function () {
    $position = openRankPosition();

    $this->actingAs($this->user)->get(route('staff-applications.show', $position))->assertOk();

    $this->actingAs($this->user)
        ->post(route('staff-applications.store', $position), [
            'content' => 'I would love to help moderate the hotel.',
        ])
        ->assertRedirect(route('staff-applications.index'))
        ->assertSessionHas('success');

    $application = WebsiteStaffApplications::where('user_id', $this->user->id)->first();

    expect($application)
        ->not->toBeNull()
        ->and($application->application_key)
        ->toBe("rank:{$this->user->id}:{$position->permission_id}");

    $this->actingAs($this->user)
        ->post(route('staff-applications.store', $position), [
            'content' => 'Trying to apply a second time.',
        ])
        ->assertSessionHasErrors('content');

    expect(WebsiteStaffApplications::where('user_id', $this->user->id)->count())->toBe(1);
});

test('a user can apply for a team once', function () {
    $position = openTeamPosition();

    $this->actingAs($this->user)
        ->post(route('team-applications.store', $position), [
            'content' => 'I would love to host community events.',
        ])
        ->assertRedirect(route('team-applications.index'))
        ->assertSessionHas('success');

    expect(WebsiteStaffApplications::where('user_id', $this->user->id)->count())->toBe(1);

    $this->actingAs($this->user)
        ->post(route('team-applications.store', $position), [
            'content' => 'Trying to apply a second time.',
        ])
        ->assertSessionHasErrors('content');

    expect(WebsiteStaffApplications::where('user_id', $this->user->id)->count())->toBe(1);
});

test('a team position rejects the rank application flow', function () {
    $position = openTeamPosition();

    $this->actingAs($this->user)
        ->post(route('staff-applications.store', $position), [
            'content' => 'Wrong flow for this position kind.',
        ])
        ->assertNotFound();
});

test('an application cannot be submitted outside the open period', function () {
    $position = openRankPosition();
    $position->update(['apply_to' => now()->subMinute()]);

    $this->actingAs($this->user)
        ->get(route('staff-applications.show', $position))
        ->assertNotFound();

    $this->actingAs($this->user)
        ->post(route('staff-applications.store', $position), [
            'content' => 'This position has already closed.',
        ])
        ->assertNotFound();

    expect(WebsiteStaffApplications::where('user_id', $this->user->id)->exists())->toBeFalse();
});

test('a position without date bounds remains open for applications', function () {
    $position = openRankPosition();
    $position->update([
        'apply_from' => null,
        'apply_to' => null,
    ]);

    $this->actingAs($this->user)
        ->get(route('staff-applications.show', $position))
        ->assertOk();

    $this->actingAs($this->user)
        ->post(route('staff-applications.store', $position), [
            'content' => 'I would love to help moderate the hotel.',
        ])
        ->assertSessionHas('success');

    expect($position->fresh()->isAcceptingApplications())->toBeTrue()
        ->and(WebsiteStaffApplications::where('user_id', $this->user->id)->exists())->toBeTrue();
});

test('application content has a bounded size', function () {
    $position = openTeamPosition();

    $this->actingAs($this->user)
        ->post(route('team-applications.store', $position), [
            'content' => str_repeat('a', 5001),
        ])
        ->assertSessionHasErrors('content');

    expect(WebsiteStaffApplications::where('user_id', $this->user->id)->exists())->toBeFalse();
});

test('deleting a rank position cascades its applications', function () {
    $position = openRankPosition();

    $this->actingAs($this->user)
        ->post(route('staff-applications.store', $position), [
            'content' => 'I would love to help moderate the hotel.',
        ])
        ->assertSessionHas('success');

    expect(WebsiteStaffApplications::where('rank_id', $position->permission_id)->exists())->toBeTrue();

    $position->delete();

    expect(WebsiteStaffApplications::where('rank_id', $position->permission_id)->exists())->toBeFalse()
        ->and(WebsiteOpenPosition::query()->count())->toBe(0);
});

test('team application API shows only the requesting users status', function (string $status) {
    $position = openTeamPosition();
    $application = app(SubmitStaffApplication::class)->forPosition($this->user, $position, 'I would like to help run events.');
    $application->update(['status' => $status]);

    $this->actingAs($this->user)->getJson('/api/v1/applications/' . $position->id)->assertOk()
        ->assertJsonPath('data.application_status', $status);
    $this->getJson('/api/v1/applications?kind=team')->assertOk()
        ->assertJsonPath('data.0.application_status', $status);

    $this->app['auth']->forgetGuards();
    $other = User::factory()->create();
    $this->actingAs($other)->getJson('/api/v1/applications/' . $position->id)->assertOk()
        ->assertJsonPath('data.application_status', null);
    $this->getJson('/api/v1/applications?kind=team')->assertOk()
        ->assertJsonPath('data.0.application_status', null);
})->with(['pending', 'approved', 'rejected']);
