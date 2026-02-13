<?php

use App\Models\Miscellaneous\WebsiteInstallation;
use App\Models\Miscellaneous\WebsiteSetting;
use App\Services\InstallationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function installHotel(): void
{
    WebsiteInstallation::query()->insert(['completed' => true, 'installation_key' => 'key']);
    InstallationService::setComplete();

    WebsiteSetting::query()->insert([
        'key' => 'max_accounts_per_ip',
        'value' => 10,
        'comment' => '',
    ]);

    WebsiteSetting::query()->insert([
        'key' => 'min_staff_rank',
        'value' => 5,
        'comment' => '',
    ]);

    WebsiteSetting::query()->insert([
        'key' => 'max_guestbook_posts_per_profile',
        'value' => 3,
        'comment' => '',
    ]);
}
