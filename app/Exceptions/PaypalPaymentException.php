<?php

namespace App\Exceptions;

use RuntimeException;

class PaypalPaymentException extends RuntimeException
{
    public static function gatewayFailure(?\Throwable $previous = null): self
    {
        return new self('PayPal could not process the request.', previous: $previous);
    }

    public static function invalidResponse(): self
    {
        return new self('PayPal returned an invalid payment response.');
    }

    public static function captureMismatch(): self
    {
        return new self('The PayPal capture did not match the expected payment.');
    }
}
