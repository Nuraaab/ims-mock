<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterOrganizationRequest;
use App\Http\Resources\Auth\OrganizationResource;
use App\Http\Resources\Auth\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function registerOrganization(RegisterOrganizationRequest $request): JsonResponse
    {
        $result = $this->authService->registerOrganization($request->validated());

        return response()->json([
            'message' => 'Organization registered successfully.',
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
            'permissions' => $this->authService->getUserPermissionKeys($result['user']),
            'organization' => new OrganizationResource($result['organization']),
        ], 201);
    }

    public function registrationLookups(): JsonResponse
    {
        return response()->json($this->authService->getRegistrationLookups());
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'Login successful.',
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
            'permissions' => $this->authService->getUserPermissionKeys($result['user']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
            'permissions' => $this->authService->getUserPermissionKeys($request->user()),
        ]);
    }
}
