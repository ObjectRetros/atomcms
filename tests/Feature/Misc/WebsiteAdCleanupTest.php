<?php

use App\Models\WebsiteAd;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    installHotel();
});

test('deleting an ad removes its image file', function () {
    $root = storage_path('framework/testing/ads');
    File::ensureDirectoryExists($root);
    File::put($root . '/banner.png', 'fake-image');

    setSetting('ads_path_filesystem', $root);

    $ad = WebsiteAd::query()->create(['image' => 'banner.png']);

    $ad->delete();

    expect(File::exists($root . '/banner.png'))->toBeFalse()
        ->and(WebsiteAd::query()->count())->toBe(0);

    File::deleteDirectory($root);
});

test('deleting an ad with a missing image file still deletes the record', function () {
    $root = storage_path('framework/testing/ads');
    File::ensureDirectoryExists($root);

    setSetting('ads_path_filesystem', $root);

    $ad = WebsiteAd::query()->create(['image' => 'does-not-exist.png']);

    $ad->delete();

    expect(WebsiteAd::query()->count())->toBe(0);

    File::deleteDirectory($root);
});
