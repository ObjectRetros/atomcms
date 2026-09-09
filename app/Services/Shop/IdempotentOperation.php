<?php

namespace App\Services\Shop;

use App\Models\User;
use App\Models\WebsiteApiIdempotencyKey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class IdempotentOperation
{
    /**
     * @param  array<string, mixed>  $input
     * @param  callable(string): array<string, mixed>  $operation
     *
     * @return array<string, mixed>
     */
    public function execute(User $actor, string $name, ?string $key, array $input, callable $operation): array
    {
        Validator::make(['idempotency_key' => $key], ['idempotency_key' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9._:-]+$/']])->validate();
        $hash = hash('sha256', json_encode($input, JSON_THROW_ON_ERROR));
        $record = WebsiteApiIdempotencyKey::firstOrCreate(['user_id' => $actor->id, 'operation' => $name, 'key_hash' => hash('sha256', (string) $key)], ['request_hash' => $hash, 'operation_reference' => (string) Str::uuid()]);

        return DB::transaction(function () use ($record, $hash, $operation, $name): array {
            $locked = WebsiteApiIdempotencyKey::whereKey($record->id)->lockForUpdate()->firstOrFail();
            if (! hash_equals($locked->request_hash, $hash)) {
                throw new ConflictHttpException('This idempotency key was already used with different input.');
            }
            if ($locked->response !== null) {
                return $locked->response;
            }
            if ($name === 'paypal-order' && ($locked->created_at === null || $locked->created_at->lte(now()->subHours(6)))) {
                throw new ConflictHttpException('This unresolved PayPal order needs reconciliation before it can be retried.');
            }
            $response = $operation('atom-order-' . $locked->operation_reference);
            $locked->update(['response' => $response, 'expires_at' => now()->addDays(30)]);

            return $response;
        });
    }
}
