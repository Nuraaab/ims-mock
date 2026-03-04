<?php

namespace Modules\IMS\Http\Controllers;

use App\Services\Auth\PermissionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\IMS\Services\Item\ItemService;

class ItemController extends Controller
{
    public function __construct(
        private readonly ItemService $itemService,
        private readonly PermissionService $permissionService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'items.view');

        $perPage = max(1, min(100, (int) $request->integer('per_page', 15)));
        $items = $this->itemService->paginate($perPage);

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
        $this->permissionService->authorize($request->user(), 'items.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:items,name'],
            'item_category_id' => ['nullable', 'integer', 'exists:item_categories,id'],
            'item_type' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $item = $this->itemService->create($validated);

        return response()->json([
            'message' => 'Item created successfully.',
            'item' => $item,
        ], 201);
    }

    public function show(Request $request, int $item): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'items.view');
        $record = $this->itemService->find($item);

        return response()->json([
            'item' => $record,
        ]);
    }

    public function update(Request $request, int $item): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'items.update');

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('items', 'name')->ignore($item)],
            'item_category_id' => ['sometimes', 'nullable', 'integer', 'exists:item_categories,id'],
            'item_type' => ['sometimes', 'nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $record = $this->itemService->update($item, $validated);

        return response()->json([
            'message' => 'Item updated successfully.',
            'item' => $record,
        ]);
    }

    public function destroy(Request $request, int $item): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'items.delete');
        $this->itemService->delete($item);

        return response()->json([
            'message' => 'Item deleted successfully.',
        ]);
    }
}
