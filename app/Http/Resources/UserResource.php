<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->resource ?: 'No user found',
        ];
    }
}
