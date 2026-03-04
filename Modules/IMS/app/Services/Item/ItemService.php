<?php

namespace Modules\IMS\Services\Item;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Modules\IMS\Models\Item;

class ItemService
{
    public function paginate(User $actor, int $perPage = 15): LengthAwarePaginator
    {
        $this->assertHasScope($actor, 'items.view');

        return Item::query()
            ->with('category:id,name')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(User $actor, array $payload): Item
    {
        $this->assertHasScope($actor, 'items.create');

        return Item::create([
            'name' => $payload['name'],
            'item_category_id' => $payload['item_category_id'] ?? null,
            'item_type' => $payload['item_type'] ?? 'product',
            'is_active' => $payload['is_active'] ?? true,
        ]);
    }

    public function find(User $actor, int $id): Item
    {
        $this->assertHasScope($actor, 'items.view');

        return Item::query()
            ->with('category:id,name')
            ->findOrFail($id);
    }

    public function update(User $actor, int $id, array $payload): Item
    {
        $this->assertHasScope($actor, 'items.update');

        $item = Item::query()->findOrFail($id);
        $item->update([
            'name' => $payload['name'] ?? $item->name,
            'item_category_id' => array_key_exists('item_category_id', $payload) ? $payload['item_category_id'] : $item->item_category_id,
            'item_type' => array_key_exists('item_type', $payload) ? $payload['item_type'] : $item->item_type,
            'is_active' => array_key_exists('is_active', $payload) ? $payload['is_active'] : $item->is_active,
        ]);

        return $this->find($actor, $item->id);
    }

    public function delete(User $actor, int $id): void
    {
        $this->assertHasScope($actor, 'items.delete');

        $item = Item::query()->findOrFail($id);
        $item->delete();
    }

    private function assertHasScope(User $actor, string $permissionKey): void
    {
        $organizationIds = scope_ids($actor, $permissionKey, 'organization');
        $branchIds = scope_ids($actor, $permissionKey, 'branch');
        $warehouseIds = scope_ids($actor, $permissionKey, 'warehouse');
        $outletIds = scope_ids($actor, $permissionKey, 'outlet');

        if (! empty($organizationIds) || ! empty($branchIds) || ! empty($warehouseIds) || ! empty($outletIds)) {
            return;
        }

        if (permission_service()->hasPermission($actor, $permissionKey)) {
            return;
        }

        throw ValidationException::withMessages([
            'organization_id' => ['No accessible scope found for the requested action.'],
        ]);
    }
}
