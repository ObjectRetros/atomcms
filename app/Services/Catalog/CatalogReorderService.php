<?php

namespace App\Services\Catalog;

use App\Models\Game\Furniture\CatalogItem;
use App\Models\Game\Furniture\CatalogPage;
use Illuminate\Support\Facades\DB;

/**
 * Mutates catalog page/item ordering. Every public method runs in a single
 * transaction, normalises spacing to (i+1)*10 so future inserts get clean room
 * to slot in, and busts the tree cache so the UI sees the change immediately.
 *
 * Locked items (order_number = -1) are intentionally excluded from any ordering
 * operation — the editor renders them in a separate "Locked" panel.
 */
class CatalogReorderService
{
    public function __construct(private readonly CatalogTreeService $tree) {}

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

        $this->tree->flushCache();
    }

    /**
     * Move a page across parents (cross-tree drag). Sets the new parent and
     * re-numbers all sibling pages under the destination so the dropped page
     * lands at $insertAtIndex (0-based).
     *
     * @param  int|null  $newParentId  -1 for root, or any catalog_pages.id
     * @param  int  $insertAtIndex  position in the new parent's child list
     */
    public function movePage(int $pageId, int $newParentId, int $insertAtIndex): void
    {
        $page = CatalogPage::find($pageId);
        if (! $page) {
            return;
        }

        DB::transaction(function () use ($page, $newParentId, $insertAtIndex) {
            // Pull current siblings (excluding the moving page if same parent)
            $siblings = CatalogPage::query()
                ->where('parent_id', $newParentId)
                ->where('id', '!=', $page->id)
                ->orderBy('order_num')
                ->orderBy('id')
                ->pluck('id')
                ->all();

            // Insert moving page at the requested index
            $insertAtIndex = max(0, min($insertAtIndex, count($siblings)));
            array_splice($siblings, $insertAtIndex, 0, [$page->id]);

            $page->update(['parent_id' => $newParentId]);

            foreach ($siblings as $i => $id) {
                CatalogPage::whereKey($id)->update(['order_num' => ($i + 1) * 10]);
            }
        });

        $this->tree->flushCache();
    }

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

    /**
     * Sorts the page's items A→Z by catalog_name and assigns 10, 20, 30…
     */
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

    /**
     * Re-spaces existing item order — preserves the relative order but resets
     * the gaps to 10 so future drags have somewhere to land.
     */
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
     * Move N items to a different page; appends to the destination's tail.
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
     * @return array<int, int>
     */
    private function cleanIds(array $ids): array
    {
        return array_values(array_unique(array_filter(
            array_map(fn ($v) => (int) $v, $ids),
            fn ($v) => $v > 0,
        )));
    }
}
