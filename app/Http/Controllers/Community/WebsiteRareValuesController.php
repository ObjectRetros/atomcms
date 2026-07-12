<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Requests\RareSearchFormRequest;
use App\Models\Community\RareValue\WebsiteRareValue;
use App\Models\Community\RareValue\WebsiteRareValueCategory;
use App\Models\Game\Furniture\Item;
use App\Models\User;
use App\Services\Community\RareValues\RareValueCategoriesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class WebsiteRareValuesController extends Controller
{
    public function __construct(private readonly RareValueCategoriesService $valueCategoriesService) {}

    public function index(): View
    {
        return view('rare-values', [
            'categories' => $this->valueCategoriesService->fetchCategoriesByPriority(),
            'categoriesNav' => $this->valueCategoriesService->fetchAllCategories(),
        ]);
    }

    public function category(int $id): View|RedirectResponse
    {
        $category = $this->valueCategoriesService->fetchCategoryById($id);

        if (! $category) {
            return redirect()->back()->withErrors([
                'message' => __('The entered category does not exist'),
            ]);
        }

        return view('rare-values', [
            'categories' => $category,
            'categoriesNav' => $this->valueCategoriesService->fetchAllCategories(),
        ]);
    }

    public function search(RareSearchFormRequest $request): View|RedirectResponse
    {
        $searchTerm = $request->input('search');

        $categories = $this->valueCategoriesService->searchCategories($searchTerm);

        if ($categories->isEmpty()) {
            return redirect()->back()->withErrors([
                'message' => __('It seems like there were no rares matching your search input'),
            ]);
        }

        return view('rare-values', [
            'categories' => $categories,
            'categoriesNav' => WebsiteRareValueCategory::has('furniture')->get(),
        ]);
    }

    public function value(WebsiteRareValue $value): View
    {
        return view('value', [
            'value' => $value,
            'items' => $this->itemsPerUser($value),
        ]);
    }

    /**
     * Count holdings per user with an aggregate query, then load only those
     * users (the page previously hydrated every furniture instance and grouped
     * them in PHP).
     *
     * @return array<int, array{user: ?User, item_count: int}>
     */
    private function itemsPerUser(WebsiteRareValue $value): array
    {
        $resolve = function () use ($value): array {
            $counts = Item::query()
                ->where('item_id', $value->item_id)
                ->groupBy('user_id')
                ->selectRaw('user_id, COUNT(*) as item_count')
                ->orderByDesc('item_count')
                ->limit(100)
                ->pluck('item_count', 'user_id');

            $users = User::whereKey($counts->keys())->get(['id', 'username', 'look'])->keyBy('id');

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
