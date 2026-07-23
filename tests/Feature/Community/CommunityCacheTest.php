<?php

use App\Models\Community\Teams\WebsiteTeam;
use App\Models\Game\Permission;
use App\Models\User;
use App\Services\Community\StaffService;
use App\Services\Community\TeamService;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    installHotel();
    setSetting('enable_caching', '1');
    setSetting('cache_timer', '60');
    setSetting('min_staff_rank', '5');
    setSetting('min_rank_to_see_hidden_staff', '7');
    Cache::flush();
});

test('a privileged staff cache cannot expose hidden staff to regular users', function () {
    Permission::whereKey(5)->update(['hidden_rank' => false]);
    Permission::whereKey(6)->update(['hidden_rank' => true]);

    User::factory()->create([
        'username' => 'VisibleStaff',
        'rank' => 5,
        'hidden_staff' => false,
    ]);
    User::factory()->create([
        'username' => 'HiddenStaff',
        'rank' => 5,
        'hidden_staff' => true,
    ]);
    User::factory()->create([
        'username' => 'HiddenRankStaff',
        'rank' => 6,
        'hidden_staff' => false,
    ]);

    $privilegedViewer = User::factory()->create(['rank' => 7]);
    $regularViewer = User::factory()->create(['rank' => 1]);
    $service = app(StaffService::class);

    $privilegedNames = $service->fetchStaffPositions($privilegedViewer)
        ->flatMap->users
        ->pluck('username');
    $publicNames = $service->fetchStaffPositions($regularViewer)
        ->flatMap->users
        ->pluck('username');

    expect($privilegedNames)
        ->toContain('HiddenStaff', 'HiddenRankStaff')
        ->and($publicNames)
        ->toContain('VisibleStaff')
        ->not->toContain('HiddenStaff', 'HiddenRankStaff');
});

test('staff caches are invalidated when displayed users and permissions change', function () {
    $viewer = User::factory()->create(['rank' => 1]);
    $staff = User::factory()->create([
        'username' => 'CacheStaff',
        'rank' => 5,
        'motto' => 'Before',
    ]);
    $service = app(StaffService::class);

    expect($service->fetchStaffPositions($viewer)->flatMap->users->firstWhere('id', $staff->id)->motto)
        ->toBe('Before');

    $staff->update(['motto' => 'After']);

    expect($service->fetchStaffPositions($viewer)->flatMap->users->firstWhere('id', $staff->id)->motto)
        ->toBe('After')
        ->and($service->fetchEmployeeIds())
        ->toContain($staff->id);

    $staff->forceFill(['rank' => 1])->save();

    expect($service->fetchEmployeeIds())->not->toContain($staff->id);

    Permission::findOrFail(5)->update(['rank_name' => 'Cache Updated Rank']);
    $staff->forceFill(['rank' => 5])->save();

    expect($service->fetchStaffPositions($viewer)->firstWhere('id', 5)->rank_name)
        ->toBe('Cache Updated Rank');
});

test('staff visibility setting changes invalidate cached results', function () {
    $viewer = User::factory()->create(['rank' => 1]);
    $staff = User::factory()->create(['rank' => 5]);
    $service = app(StaffService::class);

    expect($service->fetchStaffPositions($viewer)->flatMap->users->pluck('id'))
        ->toContain($staff->id);

    setSetting('min_staff_rank', '6');

    expect($service->fetchStaffPositions($viewer)->flatMap->users->pluck('id'))
        ->not->toContain($staff->id);
});

test('team caches are invalidated when teams or their members change', function () {
    $team = WebsiteTeam::create([
        'rank_name' => 'Event Team',
        'badge' => 'EVT',
        'hidden_rank' => false,
        'staff_color' => '#ffffff',
        'staff_background' => '',
        'job_description' => 'Runs events',
    ]);
    $member = User::factory()->create([
        'username' => 'TeamMember',
        'team_id' => $team->id,
        'motto' => 'Before',
    ]);
    $service = app(TeamService::class);

    expect($service->fetchTeams()->firstWhere('id', $team->id)->users->firstWhere('id', $member->id)->motto)
        ->toBe('Before');

    $member->update(['motto' => 'After']);

    expect($service->fetchTeams()->firstWhere('id', $team->id)->users->firstWhere('id', $member->id)->motto)
        ->toBe('After');

    $team->update(['rank_name' => 'Updated Event Team']);

    expect($service->fetchTeams()->firstWhere('id', $team->id)->rank_name)
        ->toBe('Updated Event Team');
});
