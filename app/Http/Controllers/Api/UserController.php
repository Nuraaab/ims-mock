<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\User\CreateStaffRequest;
use App\Http\Requests\User\UpdateStaffRequest;
use App\Http\Resources\Auth\UserResource;
use App\Services\Auth\PermissionService;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly PermissionService $permissionService
    )
    {
    }

    public function registerUser(RegisterUserRequest $request): JsonResponse
    {
        $result = $this->userService->registerUser($request->validated());

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => new UserResource($result['user']),
        ], 201);
    }

    public function createStaff(CreateStaffRequest $request): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'users.create');

        $result = $this->userService->createStaff($request->user(), $request->validated());

        return response()->json([
            'message' => 'Staff user created successfully.',
            'user' => new UserResource($result['user']),
            'role' => [
                'id' => $result['role']->id,
                'name' => $result['role']->name,
            ],
            'binding' => [
                'id' => $result['binding']->id,
                'scope' => $result['binding']->scope,
                'scope_id' => $result['binding']->scope_id,
                'include_descendents' => (bool) $result['binding']->include_descendents,
            ],
            'organization_id' => $result['organization_id'],
        ], 201);
    }

    public function staffLookups(Request $request): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'users.view');

        $organizationId = $request->integer('organization_id') ?: null;

        return response()->json(
            $this->userService->getStaffLookups($request->user(), $organizationId)
        );
    }

    public function listStaff(Request $request): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'users.view');

        $organizationId = $request->integer('organization_id') ?: null;
        $bindings = $this->userService->listStaff($request->user(), $organizationId);

        $staff = $bindings->map(function ($binding) {
            return [
                'user' => (new UserResource($binding->user))->resolve(),
                'role' => [
                    'id' => $binding->role->id,
                    'name' => $binding->role->name,
                ],
                'binding' => [
                    'id' => $binding->id,
                    'scope' => $binding->scope,
                    'scope_id' => $binding->scope_id,
                    'include_descendents' => (bool) $binding->include_descendents,
                ],
            ];
        })->values();

        return response()->json(['staff' => $staff]);
    }

    public function updateStaff(UpdateStaffRequest $request, int $user): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'users.update');

        $result = $this->userService->updateStaff($request->user(), $user, $request->validated());

        return response()->json([
            'message' => 'Staff user updated successfully.',
            'user' => new UserResource($result['user']),
            'role' => [
                'id' => $result['role']->id,
                'name' => $result['role']->name,
            ],
            'binding' => [
                'id' => $result['binding']->id,
                'scope' => $result['binding']->scope,
                'scope_id' => $result['binding']->scope_id,
                'include_descendents' => (bool) $result['binding']->include_descendents,
            ],
            'organization_id' => $result['organization_id'],
        ]);
    }

    public function deleteStaff(Request $request, int $user): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'users.delete');

        $this->userService->deleteStaff($request->user(), $user);

        return response()->json([
            'message' => 'Staff user deleted successfully.',
        ]);
    }
}
