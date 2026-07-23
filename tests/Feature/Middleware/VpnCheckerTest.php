<?php

use App\Models\Miscellaneous\WebsiteIpBlacklist;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    installHotel();

    setSetting('vpn_block_enabled', '1');
    setSetting('ipdata_api_key', 'testing-key');

    $this->user = User::factory()->create(['auth_ticket' => '']);
});

test('the ip reputation verdict is cached across client requests', function () {
    Http::fake([
        'api.ipdata.co/*' => Http::response([
            'ip' => '127.0.0.1',
            'asn' => ['asn' => 'AS64500'],
            'threat' => ['is_known_attacker' => false],
        ]),
    ]);

    $this->actingAs($this->user)->get(route('flash-client'))->assertOk();
    $this->actingAs($this->user)->get(route('flash-client'))->assertOk();

    Http::assertSentCount(1);
});

test('a flagged ip is blacklisted and restricted', function () {
    Http::fake([
        'api.ipdata.co/*' => Http::response([
            'ip' => '127.0.0.1',
            'asn' => ['asn' => 'AS64500'],
            'threat' => ['is_known_attacker' => true],
        ]),
    ]);

    $this->actingAs($this->user)
        ->get(route('flash-client'))
        ->assertRedirect(route('me.show'));

    expect(WebsiteIpBlacklist::where('ip_address', '127.0.0.1')->exists())->toBeTrue();
});

test('a failed lookup fails open and is not cached for the long ttl', function () {
    $attempts = 0;

    // OutboundHttp retries twice, so the first request consumes up to three
    // attempts; every one of them must fail for the lookup to fail.
    Http::fake(function () use (&$attempts) {
        if (++$attempts <= 3) {
            throw new ConnectionException('reputation service down');
        }

        return Http::response([
            'ip' => '127.0.0.1',
            'asn' => ['asn' => 'AS64500'],
            'threat' => ['is_known_attacker' => true],
        ]);
    });

    $this->actingAs($this->user)->get(route('flash-client'))->assertOk();

    // After the short failure TTL a new verdict is fetched; a flagged IP is
    // then restricted instead of riding a 12 hour fail-open cache entry.
    $this->travel(2)->minutes();

    $this->actingAs($this->user)
        ->get(route('flash-client'))
        ->assertRedirect(route('me.show'));
});
