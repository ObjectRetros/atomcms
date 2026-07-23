<?php

use App\Providers\EmulatorServiceProvider;

test('an unknown emulator driver fails fast at registration', function () {
    config(['emulator.driver' => 'bogus']);

    $provider = new EmulatorServiceProvider($this->app);

    expect(fn () => $provider->register())
        ->toThrow(InvalidArgumentException::class, 'Unknown emulator driver [bogus]');
});
