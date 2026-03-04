<?php

namespace Modules\IMS\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Modules\IMS\Models\ItemCategory;

class ItemCategoryService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return ItemCategory::query()
            ->with('parent:id,name')
            ->withCount('children')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $payload): ItemCategory
    {
        $parentId = $payload['parent_id'] ?? null;
        $level = $this->resolveLevel($parentId);

        return ItemCategory::create([
            'name' => $payload['name'],
            'code' => $payload['code'] ?? null,
            'parent_id' => $parentId,
            'level' => $level,
        ]);
    }

    public function find(int $id): ItemCategory
    {
        return ItemCategory::query()
            ->with('parent:id,name')
            ->withCount('children')
            ->findOrFail($id);
    }

    public function update(int $id, array $payload): ItemCategory
    {
        $category = ItemCategory::query()->findOrFail($id);

        $parentId = $payload['parent_id'] ?? null;
        $level = $this->resolveLevel($parentId);

        $category->update([
            'name' => $payload['name'],
            'code' => $payload['code'] ?? null,
            'parent_id' => $parentId,
            'level' => $level,
        ]);

        $this->updateDescendantLevels($category);

        return $this->find($category->id);
    }

    public function delete(int $id): void
    {
        $category = ItemCategory::query()
            ->withCount(['children', 'items'])
            ->findOrFail($id);

        if ($category->children_count > 0) {
            throw ValidationException::withMessages([
                'item_category' => ['Cannot delete category with child categories.'],
            ]);
        }

        if ($category->items_count > 0) {
            throw ValidationException::withMessages([
                'item_category' => ['Cannot delete category that is assigned to items.'],
            ]);
        }

        $category->delete();
    }

    private function resolveLevel(?int $parentId): int
    {
        if (! $parentId) {
            return 0;
        }

        $parent = ItemCategory::query()->findOrFail($parentId);

        return ((int) $parent->level) + 1;
    }

    private function updateDescendantLevels(ItemCategory $category): void
    {
        $children = ItemCategory::query()
            ->where('parent_id', $category->id)
            ->get();

        foreach ($children as $child) {
            $child->level = ((int) $category->level) + 1;
            $child->save();
            $this->updateDescendantLevels($child);
        }
    }
}
