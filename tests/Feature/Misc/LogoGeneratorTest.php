<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    installHotel();
});

test('staff can upload a generated logo', function () {
    $staff = User::factory()->create(['rank' => 7]);

    $this->actingAs($staff)
        ->post(route('store.generated-logo'), [
            'logo' => UploadedFile::fake()->image('logo.png', 100, 40),
        ])
        ->assertOk()
        ->assertJson(['success' => true]);

    $logoPath = setting('cms_logo');

    try {
        expect($logoPath)->toContain('generated-logos')
            ->and(file_exists(public_path(ltrim((string) $logoPath, '/'))))->toBeTrue();
    } finally {
        File::deleteDirectory(public_path('assets/images/generated-logos'));
    }
});

test('users without the permission cannot replace the logo', function () {
    $user = User::factory()->create(['rank' => 1]);

    $this->actingAs($user)
        ->post(route('store.generated-logo'), [
            'logo' => UploadedFile::fake()->image('logo.png', 100, 40),
        ])
        ->assertForbidden();

    expect(setting('cms_logo'))->not->toContain('generated-logos');
});
