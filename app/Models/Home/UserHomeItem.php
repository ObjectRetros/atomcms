<?php

namespace App\Models\Home;

use App\Enums\HomeItemType;
use App\Models\User;
use App\Services\Home\HomeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Transient, view-only attributes built at render time. They are set on the
 * model so they serialise to the home page JSON, but are never persisted as
 * columns:
 *
 * @property string|null $parsed_data
 * @property string|null $content
 * @property string|null $widget_type
 */
class UserHomeItem extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'placed' => 'boolean',
            'is_reversed' => 'boolean',
            'item_ids' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<HomeItem, $this>
     */
    public function homeItem(): BelongsTo
    {
        return $this->belongsTo(HomeItem::class);
    }

    /** @param Builder<static> $query */
    public function scopeDefaultRelationships(Builder $query, bool $completeLoading = false): void
    {
        $relation = $completeLoading ? 'homeItem' : 'homeItem:id,type,name,image';

        $query->with($relation);
    }

    public function setParsedData(): void
    {
        if (empty($this->extra_data)) {
            return;
        }

        $this->parsed_data = e($this->extra_data);
    }

    public function setWidgetContent(User $user): void
    {
        $this->content = null;

        if ($this->homeItem->type !== HomeItemType::Widget) {
            return;
        }

        $allAvailableWidgets = $this->homeItem->getAvailableWidgets();

        if (empty($allAvailableWidgets) || ! array_key_exists($this->homeItem->name, $allAvailableWidgets)) {
            return;
        }

        $this->widget_type = $allAvailableWidgets[$this->homeItem->name];
        $this->content = app(HomeService::class)->getWidgetContent($user, $this);
        $this->homeItem->name = __($this->homeItem->name);
    }
}
