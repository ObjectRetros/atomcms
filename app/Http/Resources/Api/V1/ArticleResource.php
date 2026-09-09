<?php

namespace App\Http\Resources\Api\V1;

use App\Data\PublicUserData;
use App\Models\Articles\WebsiteArticle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WebsiteArticle */
class ArticleResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'slug' => $this->slug, 'title' => $this->title,
            'short_story' => $this->short_story, 'full_story' => $this->full_story,
            'image' => $this->image === '' ? null : asset('storage/' . $this->image),
            'can_comment' => (bool) $this->can_comment, 'created_at' => $this->created_at?->toIso8601String(),
            'author' => $this->whenLoaded('user', fn () => $this->user ? new PublicUserResource(PublicUserData::from($this->user)) : null),
        ];
    }
}
