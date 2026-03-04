<?php

namespace Modules\IMS\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Auth\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\IMS\Http\Requests\StoreItemCategoryRequest;
use Modules\IMS\Http\Requests\UpdateItemCategoryRequest;
use Modules\IMS\Services\ItemCategoryService;
use Modules\IMS\Transformers\ItemCategoryResource;

class ItemCategoryController extends Controller
{
    public function __construct(
        private readonly ItemCategoryService $itemCategoryService,
        private readonly PermissionService $permissionService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'item_categories.view');

        $perPage = max(1, min(100, (int) $request->integer('per_page', 15)));
        $categories = $this->itemCategoryService->paginate($perPage);
        $resource = ItemCategoryResource::collection($categories->items())->resolve();
        $itemCategories = isset($resource['data']) && is_array($resource['data']) ? $resource['data'] : $resource;

        return response()->json([
            'item_categories' => $itemCategories,
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ]);
    }

    public function store(StoreItemCategoryRequest $request): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'item_categories.create');

        $category = $this->itemCategoryService->create($request->validated());

        return response()->json([
            'message' => 'Item category created successfully.',
            'item_category' => new ItemCategoryResource($category),
        ], 201);
    }

    public function show(int $item_category): JsonResponse
    {
        $this->permissionService->authorize(request()->user(), 'item_categories.view');

        $category = $this->itemCategoryService->find($item_category);

        return response()->json([
            'item_category' => new ItemCategoryResource($category),
        ]);
    }

    public function update(UpdateItemCategoryRequest $request, int $item_category): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'item_categories.update');

        $category = $this->itemCategoryService->update($item_category, $request->validated());

        return response()->json([
            'message' => 'Item category updated successfully.',
            'item_category' => new ItemCategoryResource($category),
        ]);
    }

    public function destroy(int $item_category): JsonResponse
    {
        $this->permissionService->authorize(request()->user(), 'item_categories.delete');

        $this->itemCategoryService->delete($item_category);

        return response()->json([
            'message' => 'Item category deleted successfully.',
        ]);
    }
}
