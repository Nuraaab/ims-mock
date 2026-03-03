<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\CreateWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Http\Resources\Warehouse\WarehouseResource;
use App\Services\Auth\PermissionService;
use App\Services\Warehouse\WarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function __construct(
        private readonly WarehouseService $warehouseService,
        private readonly PermissionService $permissionService
    )
    {
    }

    public function index(Request $request)
    {
        $organizationId = $request->integer('organization_id') ?: null;
        $this->permissionService->authorize($request->user(), 'warehouses.view', $organizationId);

        $perPage = max(1, min(100, (int) $request->integer('per_page', 15)));

        $warehouses = $this->warehouseService->index($request->user(), $organizationId, $perPage);

        return WarehouseResource::collection($warehouses);
    }

    public function store(CreateWarehouseRequest $request): JsonResponse
    {
        $organizationId = $request->integer('organization_id') ?: null;
        $this->permissionService->authorize($request->user(), 'warehouses.create', $organizationId);

        $warehouse = $this->warehouseService->store($request->user(), $request->validated());

        return response()->json([
            'message' => 'Warehouse created successfully.',
            'warehouse' => new WarehouseResource($warehouse),
        ], 201);
    }

    public function show(Request $request, int $warehouse): JsonResponse
    {
        $record = $this->warehouseService->show($request->user(), $warehouse);
        $this->permissionService->authorize($request->user(), 'warehouses.view', (int) $record->organization_id);

        return response()->json([
            'warehouse' => new WarehouseResource($record),
        ]);
    }

    public function update(UpdateWarehouseRequest $request, int $warehouse): JsonResponse
    {
        $existing = $this->warehouseService->show($request->user(), $warehouse);
        $this->permissionService->authorize($request->user(), 'warehouses.update', (int) $existing->organization_id);

        $record = $this->warehouseService->update($request->user(), $warehouse, $request->validated());

        return response()->json([
            'message' => 'Warehouse updated successfully.',
            'warehouse' => new WarehouseResource($record),
        ]);
    }

    public function destroy(Request $request, int $warehouse): JsonResponse
    {
        $existing = $this->warehouseService->show($request->user(), $warehouse);
        $this->permissionService->authorize($request->user(), 'warehouses.delete', (int) $existing->organization_id);

        $this->warehouseService->destroy($request->user(), $warehouse);

        return response()->json([
            'message' => 'Warehouse deleted successfully.',
        ]);
    }
}
