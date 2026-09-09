<?php

namespace App\Services\Community\RareValues;

use App\Data\PublicUserData;
use App\Emulator\Contracts\FurnitureRepository;
use App\Models\Community\RareValue\WebsiteRareValue;
use App\Models\Community\RareValue\WebsiteRareValueCategory;
use App\Models\User;
use App\Support\Sql;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

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

    /**
     * Count holdings per user with an aggregate query, then load only those
     * users (the page previously hydrated every furniture instance and grouped
     * them in PHP).
     *
     * @return array<int, array{user: ?User, item_count: int}>
     */
    public function itemsPerUser(WebsiteRareValue $value): array
    {
        $resolve = function () use ($value): array {
            $counts = app(FurnitureRepository::class)
                ->holdings((int) $value->item_id)
                ->pluck('item_count', 'user_id');

            $users = User::whereKey($counts->keys())->get(PublicUserData::COLUMNS)->keyBy('id');

            $rows = [];
            foreach ($counts as $userId => $count) {
                $rows[] = [
                    'user' => $users->get($userId),
                    'item_count' => (int) $count,
                ];
            }

            return $rows;
        };

        if (! (bool) setting('enable_caching')) {
            return $resolve();
        }

        return Cache::remember('rareItems_' . $value->id, (int) setting('cache_timer'), $resolve);
    }
}
