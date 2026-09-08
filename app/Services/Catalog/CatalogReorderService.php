<?php

namespace App\Services\Catalog;

use App\Models\Game\Furniture\CatalogItem;
use App\Models\Game\Furniture\CatalogPage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Order numbers are written as (i+1)*10 so future drops have gaps to slot into.
 * Items with order_number = -1 are "locked" and skipped by every reorder op.
 */
class CatalogReorderService
{
    /** @param  array<int, mixed>  $orderedIds */
    public function reorderPages(int $parentId, array $orderedIds): void
    {
        $clean = $this->cleanIds($orderedIds);
        if (empty($clean)) {
            return;
        }

        DB::transaction(function () use ($parentId, $clean) {
            foreach ($clean as $i => $id) {
                CatalogPage::where('parent_id', $parentId)
                    ->whereKey($id)
                    ->update(['order_num' => ($i + 1) * 10]);
            }
        });
    }

    /**
     * @param  int  $newParentId  -1 for root, or any catalog_pages.id
     * @param  int  $insertAtIndex  0-based position in the new parent's child list
     */
    public function movePage(int $pageId, int $newParentId, int $insertAtIndex): void
    {
        DB::transaction(function () use ($pageId, $newParentId, $insertAtIndex) {
            // Serialize hierarchy changes: two individually valid moves must
            // not combine into a cycle after reading the same old parents.
            $pages = CatalogPage::query()->orderBy('id')->lockForUpdate()
                ->get(['id', 'parent_id', 'order_num'])->keyBy('id');
            $page = $pages->get($pageId);

            if (! $page) {
                return;
            }

            $visited = [$pageId => true];
            $parentId = $newParentId;

            while ($parentId !== -1) {
                $parent = $pages->get($parentId);

                if ($parent === null || isset($visited[$parentId])) {
                    throw ValidationException::withMessages([
                        'parent_id' => __('Choose an existing catalog parent outside this page and its descendants.'),
                    ]);
                }

                $visited[$parentId] = true;
                $parentId = $parent->parent_id;
            }

            $siblings = CatalogPage::query()
                ->where('parent_id', $newParentId)
                ->where('id', '!=', $page->id)
                ->orderBy('order_num')
                ->orderBy('id')
                ->pluck('id')
                ->all();

            $insertAtIndex = max(0, min($insertAtIndex, count($siblings)));
            array_splice($siblings, $insertAtIndex, 0, [$page->id]);

            $page->update(['parent_id' => $newParentId]);

            foreach ($siblings as $i => $id) {
                CatalogPage::whereKey($id)->update(['order_num' => ($i + 1) * 10]);
            }
        });
    }

    /** @param  array<int, mixed>  $orderedIds */
    public function reorderItems(int $pageId, array $orderedIds): void
    {
        $clean = $this->cleanIds($orderedIds);
        if (empty($clean)) {
            return;
        }

        DB::transaction(function () use ($pageId, $clean) {
            foreach ($clean as $i => $id) {
                CatalogItem::where('page_id', $pageId)
                    ->where('order_number', '!=', -1)
                    ->whereKey($id)
                    ->update(['order_number' => ($i + 1) * 10]);
            }
        });
    }

    public function sortItemsAlphabetically(int $pageId): int
    {
        $items = CatalogItem::query()
            ->where('page_id', $pageId)
            ->where('order_number', '!=', -1)
            ->orderBy('catalog_name')
            ->orderBy('id')
            ->pluck('id');

        if ($items->isEmpty()) {
            return 0;
        }

        DB::transaction(function () use ($items) {
            foreach ($items->values() as $i => $id) {
                CatalogItem::whereKey($id)->update(['order_number' => ($i + 1) * 10]);
            }
        });

        return $items->count();
    }

    /** Preserves relative order, restores the (i+1)*10 spacing. */
    public function resetItemSpacing(int $pageId): int
    {
        $items = CatalogItem::query()
            ->where('page_id', $pageId)
            ->where('order_number', '!=', -1)
            ->orderBy('order_number')
            ->orderBy('id')
            ->pluck('id');

        if ($items->isEmpty()) {
            return 0;
        }

        DB::transaction(function () use ($items) {
            foreach ($items->values() as $i => $id) {
                CatalogItem::whereKey($id)->update(['order_number' => ($i + 1) * 10]);
            }
        });

        return $items->count();
    }

    /**
     * Appends to the destination page's tail.
     *
     * @param  array<int, mixed>  $itemIds
     */
    public function moveItemsToPage(int $targetPageId, array $itemIds): int
    {
        $clean = $this->cleanIds($itemIds);
        if (empty($clean) || ! CatalogPage::whereKey($targetPageId)->exists()) {
            return 0;
        }

        DB::transaction(function () use ($clean, $targetPageId) {
            $start = (int) (CatalogItem::where('page_id', $targetPageId)->max('order_number') ?? 0);
            foreach ($clean as $i => $id) {
                CatalogItem::whereKey($id)->update([
                    'page_id' => $targetPageId,
                    'order_number' => $start + ($i + 1) * 10,
                ]);
            }
        });

        return count($clean);
    }

    /**
     * @param  array<int, mixed>  $ids
     *
     * @return list<int>
     */
    private function cleanIds(array $ids): array
    {
        return array_values(array_unique(array_filter(
            array_map(fn ($v) => (int) $v, $ids),
            fn ($v) => $v > 0,
        )));
    }
}
