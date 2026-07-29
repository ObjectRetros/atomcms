<?php

test('a forwarded host header from a client cannot poison generated urls', function () {
    installHotel();

    $response = $this->get('/forgot-password', ['X-Forwarded-Host' => 'evil.test']);

    $response->assertOk();
    $response->assertDontSee('evil.test');

    expect(url('/'))->not->toContain('evil.test');
});

test('forwarded proto is still honoured for proxied requests', function () {
    installHotel();

    $this->get('/forgot-password', ['X-Forwarded-Proto' => 'https']);

    expect(url('/'))->toStartWith('https://');
});
