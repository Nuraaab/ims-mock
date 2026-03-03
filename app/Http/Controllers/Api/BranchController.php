<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Branch\CreateBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Http\Resources\Branch\BranchResource;
use App\Services\Auth\PermissionService;
use App\Services\Branch\BranchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $this->permissionService->authorize($request->user(), 'branches.create', $organizationId);

        $branch = $this->branchService->store($request->user(), $request->validated());

        return response()->json([
            'message' => 'Branch created successfully.',
            'branch' => new BranchResource($branch),
        ], 201);
    }

    public function show(Request $request, int $branch): JsonResponse
    {
        $record = $this->branchService->show($request->user(), $branch);
        $this->permissionService->authorize($request->user(), 'branches.view', (int) $record->organization_id);

        return response()->json([
            'branch' => new BranchResource($record),
        ]);
    }

    public function update(UpdateBranchRequest $request, int $branch): JsonResponse
    {
        $existing = $this->branchService->show($request->user(), $branch);
        $this->permissionService->authorize($request->user(), 'branches.update', (int) $existing->organization_id);

        $record = $this->branchService->update($request->user(), $branch, $request->validated());

        return response()->json([
            'message' => 'Branch updated successfully.',
            'branch' => new BranchResource($record),
        ]);
    }

    public function destroy(Request $request, int $branch): JsonResponse
    {
        $existing = $this->branchService->show($request->user(), $branch);
        $this->permissionService->authorize($request->user(), 'branches.delete', (int) $existing->organization_id);

        $this->branchService->destroy($request->user(), $branch);

        return response()->json([
            'message' => 'Branch deleted successfully.',
        ]);
    }
}
