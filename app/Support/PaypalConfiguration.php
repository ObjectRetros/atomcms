<?php

namespace App\Support;

final class PaypalConfiguration
{
    public static function isConfigured(): bool
    {
        $mode = config('habbo.paypal.mode');

        if (! is_string($mode) || ! in_array($mode, ['sandbox', 'live'], true)) {
            return false;
        }

        return filled(config("habbo.paypal.{$mode}.client_id"))
            && filled(config("habbo.paypal.{$mode}.client_secret"));
    }
}
