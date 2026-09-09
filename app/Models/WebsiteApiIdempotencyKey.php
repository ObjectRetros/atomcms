<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

/**
 * @property int $id
 * @property string $operation_reference
 * @property string $request_hash
 * @property array<string, mixed>|null $response
 */
class WebsiteApiIdempotencyKey extends Model
{
    use Prunable;

    /** @return Builder<static> */
    public function prunable(): Builder
    {
        return static::query()->whereNotNull('response')->where('expires_at', '<=', now());
    }

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['response' => 'array'];
    }
}
