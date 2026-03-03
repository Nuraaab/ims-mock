<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\RoleRequest;
use App\Http\Resources\Role\RoleResource;
use App\Services\Role\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roleService)
    {
    }

    public function CreateRole(RoleRequest $request): JsonResponse
    {
        $result = $this->roleService->CreateRole($request->validated());

        return response()->json([
            'message' => 'Role created successfully.',
            'role' => new RoleResource($result['role']),
        ], 201);
    }

    public function GetRoles(): JsonResponse
    {
        $roles = $this->roleService->getRoles();

        return response()->json([
            'roles' => RoleResource::collection($roles)->resolve(),
        ]);
    }
}
