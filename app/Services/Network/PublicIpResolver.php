<?php

namespace App\Services\Network;

use RuntimeException;

class PublicIpResolver
{
    public function resolveIpv4(string $host): string
    {
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $this->ensurePublic($host);
        }

        if (! filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new RuntimeException('The remote image host is invalid.');
        }

        $records = dns_get_record($host, DNS_A);
        $addresses = is_array($records)
            ? array_values(array_filter(array_column($records, 'ip'), 'is_string'))
            : [];

        if ($addresses === []) {
            throw new RuntimeException('The remote image host could not be resolved.');
        }

        foreach ($addresses as $address) {
            $this->ensurePublic($address);
        }

        return $addresses[0];
    }

    private function ensurePublic(string $address): string
    {
        if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            throw new RuntimeException('Remote images must resolve only to public IPv4 addresses.');
        }

        return $address;
    }
}
