<?php

namespace App\Models;

use Database\Factories\RareValueCategoriesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteRareValueCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function furniture(): HasMany
    {
        return $this->hasMany(WebsiteRareValue::class, 'category_id');
    }

    public static function newFactory(): RareValueCategoriesFactory
    {
        return RareValueCategoriesFactory::new();
    }
}