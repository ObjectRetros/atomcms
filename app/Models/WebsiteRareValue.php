<?php

namespace App\Models;

use Database\Factories\RareValuesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteRareValue extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(WebsiteRareValueCategory::class, 'category_id');
    }

    public static function newFactory(): RareValuesFactory
    {
        return RareValuesFactory::new();
    }
}