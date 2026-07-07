<?php

use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\Community\Staff\WebsiteStaffApplications;
use App\Models\Community\Staff\WebsiteTeam;
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

test('a user can apply for an open staff position once', function () {
    $position = openRankPosition();

    $this->actingAs($this->user)->get(route('staff-applications.show', $position))->assertOk();

    $this->actingAs($this->user)
        ->post(route('staff-applications.store', $position), [
            'content' => 'I would love to help moderate the hotel.',
        ])
        ->assertRedirect(route('staff-applications.index'))
        ->assertSessionHas('success');

    expect(WebsiteStaffApplications::where('user_id', $this->user->id)->count())->toBe(1);

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
