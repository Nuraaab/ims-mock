<?php

namespace Modules\IMS\Services\Item;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\IMS\Models\Item;

class ItemService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Item::query()
            ->with('category:id,name')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $payload): Item
    {
        return Item::create([
            'name' => $payload['name'],
            'item_category_id' => $payload['item_category_id'] ?? null,
            'item_type' => $payload['item_type'] ?? 'product',
            'is_active' => $payload['is_active'] ?? true,
        ]);
    }

    public function find(int $id): Item
    {
        return Item::query()
            ->with('category:id,name')
            ->findOrFail($id);
    }

    public function update(int $id, array $payload): Item
    {
        $item = Item::query()->findOrFail($id);
        $item->update([
            'name' => $payload['name'] ?? $item->name,
            'item_category_id' => array_key_exists('item_category_id', $payload) ? $payload['item_category_id'] : $item->item_category_id,
            'item_type' => array_key_exists('item_type', $payload) ? $payload['item_type'] : $item->item_type,
            'is_active' => array_key_exists('is_active', $payload) ? $payload['is_active'] : $item->is_active,
        ]);

        return $this->find($item->id);
    }

    public function delete(int $id): void
    {
        $item = Item::query()->findOrFail($id);
        $item->delete();
    }
}
