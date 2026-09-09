<?php

use App\Filament\Pages\Login;
use App\Filament\Pages\TwoFactorAuthentication;
use App\Models\User;
use Filament\Facades\Filament;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Livewire\Livewire;

test('Ada housekeeping challenges and consumes the same Fortify recovery codes', function () {
    installHotel();
    grantHousekeepingPermission('can_access_housekeeping', 6);
    setSetting('force_staff_2fa', '0');
    Filament::setCurrentPanel(Filament::getPanel('housekeeping'));
    $staff = User::factory()->create(['rank' => 6]);
    app(EnableTwoFactorAuthentication::class)($staff);
    $staff->forceFill(['two_factor_confirmed_at' => now()])->save();
    $recoveryCode = $staff->recoveryCodes()[0];

    $login = Livewire::test(Login::class)
        ->set('data.username', $staff->username)
        ->set('data.password', 'password')
        ->call('authenticate');
    $this->assertGuest();

    $login->set('data.multiFactor.fortify.code', 'invalid')
        ->call('authenticate')->assertHasErrors('data.multiFactor.fortify.code');
    $this->assertGuest();

    $login->set('data.multiFactor.fortify.code', $recoveryCode)
        ->call('authenticate')->assertHasNoErrors();
    $this->assertAuthenticatedAs($staff);
    expect($staff->fresh()->recoveryCodes())->not->toContain($recoveryCode);

    $this->get(TwoFactorAuthentication::getUrl())->assertOk();
});
