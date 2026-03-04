<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Outlet\CreateOutletRequest;
use App\Http\Requests\Outlet\UpdateOutletRequest;
use App\Http\Resources\Outlet\OutletResource;
use App\Services\Auth\PermissionService;
use App\Services\Outlet\OutletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\IMS\Models\Branch;

class OutletController extends Controller
{
    public function __construct(
        private readonly OutletService $outletService,
        private readonly PermissionService $permissionService
    )
    {
    }

    public function index(Request $request)
    {
        $organizationId = $request->integer('organization_id') ?: null;
        $this->permissionService->authorize($request->user(), 'outlets.view', $organizationId);

        $perPage = max(1, min(100, (int) $request->integer('per_page', 15)));

        $outlets = $this->outletService->index($request->user(), $organizationId, $perPage);

        return OutletResource::collection($outlets);
    }

    public function store(CreateOutletRequest $request): JsonResponse
    {
        $branch = Branch::query()->findOrFail((int) $request->input('branch_id'));
        $this->permissionService->authorize($request->user(), 'outlets.create', null, 'branch', (int) $branch->id);

        $outlet = $this->outletService->store($request->user(), $request->validated());

        return response()->json([
            'message' => 'Outlet created successfully.',
            'outlet' => new OutletResource($outlet),
        ], 201);
    }

    public function show(Request $request, int $outlet): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'outlets.view', null, 'outlet', $outlet);
        $record = $this->outletService->show($request->user(), $outlet);

        return response()->json([
            'outlet' => new OutletResource($record),
        ]);
    }

    public function update(UpdateOutletRequest $request, int $outlet): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'outlets.update', null, 'outlet', $outlet);

        if ($request->filled('branch_id')) {
            $targetBranchId = (int) $request->input('branch_id');
            $this->permissionService->authorize($request->user(), 'outlets.update', null, 'branch', $targetBranchId);
        }

        $record = $this->outletService->update($request->user(), $outlet, $request->validated());

        return response()->json([
            'message' => 'Outlet updated successfully.',
            'outlet' => new OutletResource($record),
        ]);
    }

    public function destroy(Request $request, int $outlet): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'outlets.delete', null, 'outlet', $outlet);

        $this->outletService->destroy($request->user(), $outlet);

        return response()->json([
            'message' => 'Outlet deleted successfully.',
        ]);
    }
}
