<?php

namespace App\Http\Resources\Api\V1;

use App\Data\PublicUserData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PublicUserData */
class PublicUserResource extends JsonResource
{
    /** @return array{id: int, username: string, motto: string, look: string, online: bool} */
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'username' => $this->username, 'motto' => $this->motto, 'look' => $this->look, 'online' => $this->online];
    }
}
