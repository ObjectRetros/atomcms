<?php

namespace App\Services\Community\RareValues;

use App\Models\Community\RareValue\WebsiteRareValueCategory;
use App\Support\Sql;
use Illuminate\Database\Eloquent\Collection;

class RareValueCategoriesService
{
    /** @return Collection<int, WebsiteRareValueCategory> */
    public function fetchAllCategories(): Collection
    {
        return WebsiteRareValueCategory::all();
    }

    /** @return Collection<int, WebsiteRareValueCategory> */
    public function fetchCategoriesByPriority(): Collection
    {
        return WebsiteRareValueCategory::orderBy('priority')->with('furniture')->get();
    }

    /** @return Collection<int, WebsiteRareValueCategory> */
    public function searchCategories(string $searchTerm): Collection
    {
        $like = '%' . Sql::escapeLike($searchTerm) . '%';

        return WebsiteRareValueCategory::orderBy('priority')->whereHas('furniture', function ($query) use ($like) {
            $query->where('name', 'like', $like);
        })
            ->with(['furniture' => function ($query) use ($like) {
                $query->where('name', 'like', $like);
            }])
            ->get();
    }
}
