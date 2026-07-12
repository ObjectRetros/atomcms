<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class BadgePurchaseException extends Exception
{
    public function __construct(string $message, private readonly int $status = 400)
    {
        parent::__construct($message);
    }

    public static function insufficientFunds(string $currencyType): self
    {
        return new self('Insufficient ' . $currencyType . '.');
    }

    public static function pathNotConfigured(): self
    {
        return new self('Badges path not configured.', 500);
    }

    public static function saveFailed(): self
    {
        return new self('Failed to save badge file.', 500);
    }

    public function render(): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $this->getMessage()], $this->status);
    }
}
