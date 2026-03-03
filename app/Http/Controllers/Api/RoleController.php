<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\RoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\Role\RoleResource;
use App\Services\Auth\PermissionService;
use App\Services\Role\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly PermissionService $permissionService
    )
    {
    }

    public function CreateRole(RoleRequest $request): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'roles.create');

        $result = $this->roleService->CreateRole($request->validated());

        return response()->json([
            'message' => 'Role created successfully.',
            'role' => new RoleResource($result['role']),
        ], 201);
    }

    public function GetRoles(): JsonResponse
    {
        $this->permissionService->authorize(request()->user(), 'roles.view');

        $roles = $this->roleService->getRoles();

        return response()->json([
            'roles' => RoleResource::collection($roles)->resolve(),
        ]);
    }

    public function UpdateRole(UpdateRoleRequest $request, int $role): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'roles.update');

        $result = $this->roleService->updateRole($role, $request->validated());

        return response()->json([
            'message' => 'Role updated successfully.',
            'role' => new RoleResource($result['role']),
        ]);
    }

    public function DeleteRole(int $role): JsonResponse
    {
        $this->permissionService->authorize(request()->user(), 'roles.delete');

        $this->roleService->deleteRole($role);

        return response()->json([
            'message' => 'Role deleted successfully.',
        ]);
    }
}
