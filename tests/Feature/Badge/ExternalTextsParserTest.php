<?php

use App\Services\Parsers\ExternalTextsParser;
use App\Support\BadgeCode;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

function externalTextsSetup(): ExternalTextsParser
{
    installHotel();
    Storage::fake('badges');

    $directory = storage_path('framework/testing/external-texts');
    File::ensureDirectoryExists($directory);
    File::cleanDirectory($directory);

    $nitroPath = $directory . '/ExternalTexts.json';
    file_put_contents($nitroPath, json_encode([
        'badge_name_ACH_Existing' => 'Existing badge',
        'badge_desc_ACH_Existing' => 'Already described',
        'unrelated.key' => 'untouched',
    ], JSON_PRETTY_PRINT));

    $flashPath = $directory . '/external_flash_texts.txt';
    file_put_contents($flashPath, implode("\n", [
        'badge_name_ACH_Existing=Existing badge',
        'badge_desc_ACH_Existing=Already described',
        'landing.view.me=Welcome back',
    ]) . "\n");

    setSetting('nitro_external_texts_file', $nitroPath);
    setSetting('flash_external_texts_file', $flashPath);
    setSetting('badges_path', '/testing-badges');

    return app(ExternalTextsParser::class);
}

test('searching an unknown badge code finds nothing', function () {
    $parser = externalTextsSetup();

    $data = $parser->getBadgeData('ACH_Unknown');

    expect($data['nitro'])->toBeNull()
        ->and($data['flash'])->toBeNull()
        ->and($data['image'])->toBeNull();
});

test('searching an existing badge returns its nitro and flash texts', function () {
    $parser = externalTextsSetup();

    $data = $parser->getBadgeData('ACH_Existing');

    expect($data['nitro'])->toBe(['title' => 'Existing badge', 'description' => 'Already described'])
        ->and($data['flash'])->toBe(['title' => 'Existing badge', 'description' => 'Already described']);
});

test('updating badge texts round-trips and preserves unrelated entries', function () {
    $parser = externalTextsSetup();

    // The badge page spreads its form state as named arguments - keep that
    // calling convention working.
    $parser->updateNitroBadgeTexts('ACH_New', ...['title' => 'Fresh badge', 'description' => 'Brand new']);
    $parser->updateFlashBadgeTexts('ACH_New', ...['title' => 'Fresh badge', 'description' => 'Brand new']);

    $data = $parser->getBadgeData('ACH_New');

    expect($data['nitro'])->toBe(['title' => 'Fresh badge', 'description' => 'Brand new'])
        ->and($data['flash'])->toBe(['title' => 'Fresh badge', 'description' => 'Brand new']);

    $nitro = json_decode((string) file_get_contents(storage_path('framework/testing/external-texts/ExternalTexts.json')), true);
    $flash = (string) file_get_contents(storage_path('framework/testing/external-texts/external_flash_texts.txt'));

    expect($nitro['unrelated.key'])->toBe('untouched')
        ->and($nitro['badge_name_ACH_Existing'])->toBe('Existing badge')
        ->and($flash)->toContain('landing.view.me=Welcome back')
        ->and($flash)->toContain('badge_name_ACH_New=Fresh badge');
});

test('updating an existing badge rewrites its lines in place', function () {
    $parser = externalTextsSetup();

    $parser->updateFlashBadgeTexts('ACH_Existing', title: 'Renamed', description: 'Redescribed');

    $flash = (string) file_get_contents(storage_path('framework/testing/external-texts/external_flash_texts.txt'));

    expect($flash)->toContain('badge_name_ACH_Existing=Renamed')
        ->and($flash)->toContain('badge_desc_ACH_Existing=Redescribed')
        ->and(substr_count($flash, 'badge_name_ACH_Existing='))->toBe(1);
});

test('badge codes are normalized and unsafe file keys are rejected', function () {
    $parser = externalTextsSetup();
    $nitroPath = storage_path('framework/testing/external-texts/ExternalTexts.json');
    $before = (string) file_get_contents($nitroPath);

    expect(BadgeCode::normalize(' ach_new-1 '))->toBe('ACH_NEW-1')
        ->and(fn () => BadgeCode::normalize('../badge'))->toThrow(InvalidArgumentException::class)
        ->and(fn () => $parser->updateNitroBadgeTexts('../badge', 'Unsafe', 'Unsafe'))
        ->toThrow(InvalidArgumentException::class)
        ->and((string) file_get_contents($nitroPath))->toBe($before);
});

test('malformed nitro texts are never overwritten', function () {
    $parser = externalTextsSetup();
    $nitroPath = storage_path('framework/testing/external-texts/ExternalTexts.json');
    file_put_contents($nitroPath, '{malformed');

    expect(fn () => $parser->updateNitroBadgeTexts('ACH_Safe', 'Safe', 'Safe'))
        ->toThrow(RuntimeException::class)
        ->and((string) file_get_contents($nitroPath))->toBe('{malformed');
});

test('flash text values cannot inject additional entries', function () {
    $parser = externalTextsSetup();
    $flashPath = storage_path('framework/testing/external-texts/external_flash_texts.txt');
    $before = (string) file_get_contents($flashPath);

    expect(fn () => $parser->updateFlashBadgeTexts('ACH_Safe', "Safe\ninjected.key=value", 'Safe'))
        ->toThrow(InvalidArgumentException::class)
        ->and((string) file_get_contents($flashPath))->toBe($before);
});

test('the badge image url and existence follow the configured badge disk', function () {
    $parser = externalTextsSetup();

    expect($parser->getBadgeImageUrl('ACH_Img'))->toBe(url('/testing-badges/ACH_Img.gif'));

    Storage::disk('badges')->put('ACH_Img.gif', 'gif');

    expect($parser->getBadgeData('ACH_Img')['image'])
        ->toBe(url('/testing-badges/ACH_Img.gif'));
});
