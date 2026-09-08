<?php

use App\Models\WebsiteBadge;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->badgeImportPath = tempnam(sys_get_temp_dir(), 'atom-badge-import-');
    setSetting('nitro_external_texts_file', $this->badgeImportPath);
});

afterEach(function () {
    File::delete($this->badgeImportPath);
});

test('badge imports reject malformed JSON and non-object documents', function (string $contents) {
    File::put($this->badgeImportPath, $contents);

    $this->artisan('import:badge-data')->assertFailed();

    expect(WebsiteBadge::count())->toBe(0);
})->with(['{"badge_name_TEST":', 'null', '42', '["badge_name_TEST"]']);

test('badge imports update names and descriptions without duplicating definitions', function () {
    File::put($this->badgeImportPath, json_encode([
        'badge_name_TEST' => 'First name',
        'badge_desc_TEST' => 'First description',
        'unrelated_setting' => ['nested' => true],
    ], JSON_THROW_ON_ERROR));

    $this->artisan('import:badge-data')->assertSuccessful();

    File::put($this->badgeImportPath, json_encode([
        'badge_name_TEST' => 'Updated name',
        'badge_desc_TEST' => 'Updated description',
    ], JSON_THROW_ON_ERROR));

    $this->artisan('import:badge-data')->assertSuccessful();

    expect(WebsiteBadge::count())->toBe(1)
        ->and(WebsiteBadge::first()->only(['badge_key', 'badge_name', 'badge_description']))
        ->toBe([
            'badge_key' => 'TEST',
            'badge_name' => 'Updated name',
            'badge_description' => 'Updated description',
        ]);
});

test('a failure in a later badge import chunk preserves all existing definitions', function () {
    WebsiteBadge::create([
        'badge_key' => 'TEST0',
        'badge_name' => 'Original',
        'badge_description' => 'Original description',
    ]);
    $data = [];

    for ($index = 0; $index < 100; $index++) {
        $data['badge_name_TEST' . $index] = 'Updated name';
    }

    $data['badge_name_INVALID'] = str_repeat('x', 256);
    File::put($this->badgeImportPath, json_encode($data, JSON_THROW_ON_ERROR));

    $this->artisan('import:badge-data')->assertFailed();

    expect(WebsiteBadge::count())->toBe(1)
        ->and(WebsiteBadge::first()->badge_name)->toBe('Original');
});
