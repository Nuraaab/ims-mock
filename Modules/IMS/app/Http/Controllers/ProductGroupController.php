<?php

namespace Modules\IMS\Http\Controllers;

use App\Services\Auth\PermissionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\IMS\Models\ProductGroup;
use Modules\IMS\Services\ProductGroup\ProductGroupService;

class ProductGroupController extends Controller
{
    public function __construct(
        private readonly ProductGroupService $productGroupService,
        private readonly PermissionService $permissionService
    )
    {
    }

    public function index(Request $request)
    {
        $organizationId = $request->integer('organization_id') ?: null;
        $this->permissionService->authorize($request->user(), 'product-groups.view', $organizationId);

        $perPage = max(1, min(100, (int) $request->integer('per_page', 15)));
        $productGroups = $this->productGroupService->index($request->user(), $organizationId, $perPage);

        return response()->json($productGroups);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'organization_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $branchId = $request->integer('branch_id') ?: null;

        if ($branchId !== null) {
            $this->permissionService->authorize($request->user(), 'product-groups.create', null, 'branch', $branchId);
        } else {
            $organizationId = $request->integer('organization_id') ?: null;
            $this->permissionService->authorize($request->user(), 'product-groups.create', $organizationId);
        }

        $productGroup = $this->productGroupService->store($request->user(), $validated);

        return response()->json([
            'message' => 'Product group created successfully.',
            'product_group' => $productGroup,
        ], 201);
    }

    public function show(Request $request, int $productGroup): JsonResponse
    {
        $record = ProductGroup::query()->findOrFail($productGroup);
        if ($record->branch_id) {
            $this->permissionService->authorize($request->user(), 'product-groups.view', null, 'branch', (int) $record->branch_id);
        } else {
            $this->permissionService->authorize($request->user(), 'product-groups.view', (int) $record->organization_id);
        }
        $record = $this->productGroupService->show($request->user(), $productGroup);

        return response()->json([
            'product_group' => $record,
        ]);
    }

    public function update(Request $request, int $productGroup): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => ['sometimes', 'nullable', 'integer', 'exists:branches,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $existing = ProductGroup::query()->findOrFail($productGroup);
        if ($existing->branch_id) {
            $this->permissionService->authorize($request->user(), 'product-groups.update', null, 'branch', (int) $existing->branch_id);
        } else {
            $this->permissionService->authorize($request->user(), 'product-groups.update', (int) $existing->organization_id);
        }

        if ($request->filled('branch_id')) {
            $this->permissionService->authorize($request->user(), 'product-groups.update', null, 'branch', (int) $request->input('branch_id'));
        }

        $record = $this->productGroupService->update($request->user(), $productGroup, $validated);

        return response()->json([
            'message' => 'Product group updated successfully.',
            'product_group' => $record,
        ]);
    }

    public function destroy(Request $request, int $productGroup): JsonResponse
    {
        $existing = ProductGroup::query()->findOrFail($productGroup);
        if ($existing->branch_id) {
            $this->permissionService->authorize($request->user(), 'product-groups.delete', null, 'branch', (int) $existing->branch_id);
        } else {
            $this->permissionService->authorize($request->user(), 'product-groups.delete', (int) $existing->organization_id);
        }

        $this->productGroupService->destroy($request->user(), $productGroup);

        return response()->json([
            'message' => 'Product group deleted successfully.',
        ]);
    }
}
