<?php

namespace App\Services\Community\RareValues;

use App\Models\Community\RareValue\WebsiteRareValueCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RareValueCategoriesService
{
    public function fetchAllCategories(): Collection
    {
        return WebsiteRareValueCategory::all();
    }

    public function fetchCategoriesByPriority(): Builder|Collection
    {
        return WebsiteRareValueCategory::orderBy('priority')->with('furniture')->get();
    }

    public function fetchCategoryById(int $id): ?WebsiteRareValueCategory
    {
        return WebsiteRareValueCategory::orderBy('priority')->whereId($id)->with('furniture')->first();
    }

    public function searchCategories(string $searchTerm): Collection
    {
        return WebsiteRareValueCategory::orderBy('priority')->whereHas('furniture', function ($query) use ($searchTerm) {
            $query->where('name', 'like', '%'.$searchTerm.'%');
        })
            ->with(['furniture' => function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%'.$searchTerm.'%');
            }])
            ->get();
    }
}
