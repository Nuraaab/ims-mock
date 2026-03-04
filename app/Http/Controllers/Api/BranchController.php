<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Branch\CreateBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Http\Resources\Branch\BranchResource;
use App\Services\Auth\PermissionService;
use App\Services\Branch\BranchService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BranchController extends Controller
{
    public function __construct(
        private readonly BranchService $branchService,
        private readonly PermissionService $permissionService
    )
    {
    }

    public function index(Request $request)
    {
        $organizationId = $request->integer('organization_id') ?: null;
        $this->permissionService->authorize($request->user(), 'branches.view', $organizationId);

        $perPage = max(1, min(100, (int) $request->integer('per_page', 15)));

        $branches = $this->branchService->index($request->user(), $organizationId, $perPage);

        return BranchResource::collection($branches);
    }

    public function store(CreateBranchRequest $request): JsonResponse
    {
        $organizationId = $request->integer('organization_id') ?: null;

        if ($organizationId === null) {
            $accessibleOrganizationIds = $this->permissionService
                ->getAccessibleScopeIds($request->user(), 'branches.create', 'organization');

            if (count($accessibleOrganizationIds) === 1) {
                $organizationId = (int) $accessibleOrganizationIds[0];
            } elseif (count($accessibleOrganizationIds) > 1) {
                throw ValidationException::withMessages([
                    'organization_id' => ['organization_id is required when you have access to multiple organizations.'],
                ]);
            } else {
                throw new AuthorizationException('You need organization-scoped access to create branches.');
            }
        }

        $this->permissionService->authorize($request->user(), 'branches.create', $organizationId);

        $branch = $this->branchService->store($request->user(), [
            ...$request->validated(),
            'organization_id' => $organizationId,
        ]);

        return response()->json([
            'message' => 'Branch created successfully.',
            'branch' => new BranchResource($branch),
        ], 201);
    }

    public function show(Request $request, int $branch): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'branches.view', null, 'branch', $branch);
        $record = $this->branchService->show($request->user(), $branch);

        return response()->json([
            'branch' => new BranchResource($record),
        ]);
    }

    public function update(UpdateBranchRequest $request, int $branch): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'branches.update', null, 'branch', $branch);

        $record = $this->branchService->update($request->user(), $branch, $request->validated());

        return response()->json([
            'message' => 'Branch updated successfully.',
            'branch' => new BranchResource($record),
        ]);
    }

    public function destroy(Request $request, int $branch): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'branches.delete', null, 'branch', $branch);

        $this->branchService->destroy($request->user(), $branch);

        return response()->json([
            'message' => 'Branch deleted successfully.',
        ]);
    }
}
