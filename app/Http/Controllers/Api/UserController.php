<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\User\CreateStaffRequest;
use App\Http\Resources\Auth\UserResource;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
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
        $organizationId = $request->integer('organization_id') ?: null;

        return response()->json(
            $this->userService->getStaffLookups($request->user(), $organizationId)
        );
    }
}
