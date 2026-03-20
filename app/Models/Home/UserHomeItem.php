<?php

namespace App\Models\Home;

use App\Enums\HomeItemType;
use App\Models\User;
use App\Services\Home\HomeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHomeItem extends Model
{
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function homeItem(): BelongsTo
    {
        return $this->belongsTo(HomeItem::class);
    }

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
