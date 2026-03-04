<?php

namespace Modules\IMS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\IMS\Services\Item\ItemService;

class ItemController extends Controller
{
    public function __construct(
        private readonly ItemService $itemService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorizeByScope($request, 'items.view');

        $perPage = max(1, min(100, (int) $request->integer('per_page', 15)));
        $items = $this->itemService->paginate($request->user(), $perPage);

        return response()->json([
            'items' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeByScope($request, 'items.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:items,name'],
            'item_category_id' => ['nullable', 'integer', 'exists:item_categories,id'],
            'item_type' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $item = $this->itemService->create($request->user(), $validated);

        return response()->json([
            'message' => 'Item created successfully.',
            'item' => $item,
        ], 201);
    }

    public function show(Request $request, int $item): JsonResponse
    {
        $this->authorizeByScope($request, 'items.view');
        $record = $this->itemService->find($request->user(), $item);

        return response()->json([
            'item' => $record,
        ]);
    }

    public function update(Request $request, int $item): JsonResponse
    {
        $this->authorizeByScope($request, 'items.update');

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('items', 'name')->ignore($item)],
            'item_category_id' => ['sometimes', 'nullable', 'integer', 'exists:item_categories,id'],
            'item_type' => ['sometimes', 'nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $record = $this->itemService->update($request->user(), $item, $validated);

        return response()->json([
            'message' => 'Item updated successfully.',
            'item' => $record,
        ]);
    }

    public function destroy(Request $request, int $item): JsonResponse
    {
        $this->authorizeByScope($request, 'items.delete');
        $this->itemService->delete($request->user(), $item);

        return response()->json([
            'message' => 'Item deleted successfully.',
        ]);
    }

    private function authorizeByScope(Request $request, string $permissionKey): void
    {
        $user = $request->user();
        $organizationIds = scope_ids($user, $permissionKey, 'organization');
        $branchIds = scope_ids($user, $permissionKey, 'branch');
        $warehouseIds = scope_ids($user, $permissionKey, 'warehouse');
        $outletIds = scope_ids($user, $permissionKey, 'outlet');

        if (! empty($organizationIds) || ! empty($branchIds) || ! empty($warehouseIds) || ! empty($outletIds)) {
            return;
        }

        permission_service()->authorize($user, $permissionKey);
    }
}
