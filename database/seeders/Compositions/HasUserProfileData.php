<?php

namespace Database\Seeders\Compositions;

use App\Models\Home\HomeCategory;
use Database\Seeders\Compositions\Home\HasAlhambraCategoryData;
use Database\Seeders\Compositions\Home\HasArtistsCategoryData;
use Database\Seeders\Compositions\Home\HasBackgroundsCategoryData;
use Database\Seeders\Compositions\Home\HasBlingAlphabetCategoryData;
use Database\Seeders\Compositions\Home\HasButtonsCategoryData;
use Database\Seeders\Compositions\Home\HasCineCategoryData;
use Database\Seeders\Compositions\Home\HasClampsAndRelatedCategoryData;
use Database\Seeders\Compositions\Home\HasCoinsAndRelatedCategoryData;
use Database\Seeders\Compositions\Home\HasDividersCategoryData;
use Database\Seeders\Compositions\Home\HasForestAndRelatedCategoryData;
use Database\Seeders\Compositions\Home\HasHabboweenCategoryData;
use Database\Seeders\Compositions\Home\HasKeepItRealCategoryData;
use Database\Seeders\Compositions\Home\HasNotesCategoryData;
use Database\Seeders\Compositions\Home\HasPaintingsCategoryData;
use Database\Seeders\Compositions\Home\HasPiratesCategoryData;
use Database\Seeders\Compositions\Home\HasPlasticAlphabetCategoryData;
use Database\Seeders\Compositions\Home\HasSnowStormCategoryData;
use Database\Seeders\Compositions\Home\HasSportsCategoryData;
use Database\Seeders\Compositions\Home\HasSummerVacationCategoryData;
use Database\Seeders\Compositions\Home\HasValentineCategoryData;
use Database\Seeders\Compositions\Home\HasWidgetsCategoryData;
use Database\Seeders\Compositions\Home\HasWoodenAlphabetCategoryData;
use Database\Seeders\Compositions\Home\HasWWECategoryData;

trait HasUserProfileData
{
    use HasAlhambraCategoryData,
        HasArtistsCategoryData,
        HasBackgroundsCategoryData,
        HasBlingAlphabetCategoryData,
        HasButtonsCategoryData,
        HasCineCategoryData,
        HasClampsAndRelatedCategoryData,
        HasCoinsAndRelatedCategoryData,
        HasDividersCategoryData,
        HasForestAndRelatedCategoryData,
        HasHabboweenCategoryData,
        HasKeepItRealCategoryData,
        HasNotesCategoryData,
        HasPaintingsCategoryData,
        HasPiratesCategoryData,
        HasPlasticAlphabetCategoryData,
        HasSnowStormCategoryData,
        HasSportsCategoryData,
        HasSummerVacationCategoryData,
        HasValentineCategoryData,
        HasWidgetsCategoryData,
        HasWoodenAlphabetCategoryData,
        HasWWECategoryData;

    protected function buildItemStructure(
        ?HomeCategory $category,
        string $image,
        ?string $name = null,
        int $price = 5,
        string $type = 's',
    ): array {
        return [
            'type' => $type,
            'order' => $this->currentOrder++,
            'home_category_id' => $category?->id,
            'name' => $name ?? sprintf('%s %s', config('app.name'), 'Item'),
            'image' => $image,
            'price' => $price,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
