<?php

namespace Modules\IMS\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Auth\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\IMS\Http\Requests\StoreProductRequest;
use Modules\IMS\Http\Requests\UpdateProductRequest;
use Modules\IMS\Models\Product;
use Modules\IMS\Models\ProductGroup;
use Modules\IMS\Services\Item\ProductService;
use Modules\IMS\Transformers\ProductResource;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly PermissionService $permissionService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $organizationId = $request->integer('organization_id') ?: null;
        $this->permissionService->authorize($request->user(), 'products.view', $organizationId);

        $perPage = max(1, min(100, (int) $request->integer('per_page', 15)));
        $products = $this->productService->paginate($request->user(), $organizationId, $perPage);

        return response()->json([
            'products' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $organizationId = $request->integer('organization_id') ?: null;
        $productGroupId = $request->integer('product_group_id') ?: null;

        if ($productGroupId !== null) {
            $group = ProductGroup::query()->findOrFail($productGroupId);
            if ($group->branch_id) {
                $this->permissionService->authorize($request->user(), 'products.create', null, 'branch', (int) $group->branch_id);
            } else {
                $this->permissionService->authorize($request->user(), 'products.create', (int) $group->organization_id);
            }
        } else {
            $this->permissionService->authorize($request->user(), 'products.create', $organizationId);
        }

        $product = $this->productService->create($request->user(), $request->validated());

        return response()->json([
            'message' => 'Product created successfully.',
            'product' => new ProductResource($product),
        ], 201);
    }

    public function show(Request $request, int $product): JsonResponse
    {
        $record = Product::query()->findOrFail($product);
        if ($record->product_group_id) {
            $group = ProductGroup::query()->findOrFail((int) $record->product_group_id);
            if ($group->branch_id) {
                $this->permissionService->authorize($request->user(), 'products.view', null, 'branch', (int) $group->branch_id);
            } else {
                $this->permissionService->authorize($request->user(), 'products.view', (int) $record->organization_id);
            }
        } else {
            $this->permissionService->authorize($request->user(), 'products.view', (int) $record->organization_id);
        }
        $record = $this->productService->find($request->user(), $product);

        return response()->json([
            'product' => new ProductResource($record),
        ]);
    }

    public function update(UpdateProductRequest $request, int $product): JsonResponse
    {
        $existing = Product::query()->findOrFail($product);
        if ($existing->product_group_id) {
            $group = ProductGroup::query()->findOrFail((int) $existing->product_group_id);
            if ($group->branch_id) {
                $this->permissionService->authorize($request->user(), 'products.update', null, 'branch', (int) $group->branch_id);
            } else {
                $this->permissionService->authorize($request->user(), 'products.update', (int) $existing->organization_id);
            }
        } else {
            $this->permissionService->authorize($request->user(), 'products.update', (int) $existing->organization_id);
        }

        if ($request->filled('product_group_id')) {
            $targetGroup = ProductGroup::query()->findOrFail((int) $request->input('product_group_id'));
            if ($targetGroup->branch_id) {
                $this->permissionService->authorize($request->user(), 'products.update', null, 'branch', (int) $targetGroup->branch_id);
            } else {
                $this->permissionService->authorize($request->user(), 'products.update', (int) $targetGroup->organization_id);
            }
        }

        $record = $this->productService->update($request->user(), $product, $request->validated());

        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => new ProductResource($record),
        ]);
    }

    public function destroy(Request $request, int $product): JsonResponse
    {
        $existing = Product::query()->findOrFail($product);
        if ($existing->product_group_id) {
            $group = ProductGroup::query()->findOrFail((int) $existing->product_group_id);
            if ($group->branch_id) {
                $this->permissionService->authorize($request->user(), 'products.delete', null, 'branch', (int) $group->branch_id);
            } else {
                $this->permissionService->authorize($request->user(), 'products.delete', (int) $existing->organization_id);
            }
        } else {
            $this->permissionService->authorize($request->user(), 'products.delete', (int) $existing->organization_id);
        }

        $this->productService->delete($request->user(), $product);

        return response()->json([
            'message' => 'Product deleted successfully.',
        ]);
    }
}
