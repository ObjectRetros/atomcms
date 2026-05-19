<?php

namespace App\Services\Catalog;

use App\Models\Game\Furniture\CatalogItem;
use App\Models\Game\Furniture\CatalogPage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Read-side helpers for the catalog editor: tree fetching, breadcrumbs and
 * search. Mutating ops live in CatalogReorderService so this class stays
 * pure-read and the cache invariants are obvious.
 */
class CatalogTreeService
{
    private const CACHE_KEY = 'catalog-editor.tree.v1';

    private const CACHE_TTL = 60;

    /**
     * Catalog pages grouped by parent_id and ordered by order_num.
     * Cached because the tree is rendered on every request.
     *
     * @return Collection<int, Collection<int, CatalogPage>>
     */
    public function pagesGroupedByParent(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return CatalogPage::query()
                ->orderBy('order_num')
                ->orderBy('id')
                ->get()
                ->groupBy('parent_id');
        });
    }

    public function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Walks parent_id up to the root, returning [root, …, page]. Uses a single
     * SELECT and an in-memory map so deeply nested pages don't fan out queries.
     *
     * @return array<int, CatalogPage>
     */
    public function breadcrumb(?CatalogPage $page): array
    {
        if (! $page) {
            return [];
        }

        $byId = CatalogPage::query()->get()->keyBy('id');
        $chain = [];
        $current = $byId[$page->id] ?? null;

        while ($current) {
            array_unshift($chain, $current);
            $current = $current->parent_id > 0 ? ($byId[$current->parent_id] ?? null) : null;
        }

        return $chain;
    }

    /**
     * Page IDs whose subtree should be expanded so a search hit is visible.
     *
     * @param  Collection<int, int>  $hitIds
     * @return array<int, int>
     */
    public function expandToReveal(Collection $hitIds): array
    {
        $byId = CatalogPage::query()->get(['id', 'parent_id'])->keyBy('id');
        $reveal = [];

        foreach ($hitIds as $id) {
            $cursor = $byId[$id] ?? null;
            while ($cursor) {
                $reveal[$cursor->id] = $cursor->id;
                $cursor = $cursor->parent_id > 0 ? ($byId[$cursor->parent_id] ?? null) : null;
            }
        }

        return array_values($reveal);
    }

    /**
     * @return Collection<int, CatalogPage>
     */
    public function searchPages(string $needle): Collection
    {
        $like = '%'.$this->escapeLike($needle).'%';

        return CatalogPage::query()
            ->where(function ($q) use ($like) {
                $q->whereRaw("caption LIKE ? ESCAPE '\\\\'", [$like])
                    ->orWhereRaw("caption_save LIKE ? ESCAPE '\\\\'", [$like]);
            })
            ->orderBy('order_num')
            ->limit(200)
            ->get();
    }

    /**
     * @return Collection<int, CatalogItem>
     */
    public function searchItems(string $needle): Collection
    {
        $like = '%'.$this->escapeLike($needle).'%';
        $isNumeric = ctype_digit($needle);

        return CatalogItem::query()
            ->where(function ($q) use ($like, $needle, $isNumeric) {
                $q->whereRaw("catalog_name LIKE ? ESCAPE '\\\\'", [$like]);
                if ($isNumeric) {
                    $q->orWhere('id', (int) $needle);
                }
            })
            ->orderBy('catalog_name')
            ->limit(200)
            ->get();
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
