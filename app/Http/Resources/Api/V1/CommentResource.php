<?php

namespace App\Http\Resources\Api\V1;

use App\Data\PublicUserData;
use App\Models\Articles\WebsiteArticleComment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WebsiteArticleComment */
class CommentResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'comment' => $this->comment, 'can_delete' => $request->user()?->can('delete', $this->resource) ?? false, 'created_at' => $this->created_at?->toIso8601String(), 'author' => $this->whenLoaded('user', fn () => $this->user ? new PublicUserResource(PublicUserData::from($this->user)) : null)];
    }
}
