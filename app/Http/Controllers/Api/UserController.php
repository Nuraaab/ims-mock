<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Auth\UserResource;
use App\Services\User\UserService;


class UserController extends Controller
{

public function __construct(private readonly UserService $UserService)
    {
    }
    public function RegisterUser(RegisterUserRequest $request) : JsonResponse {

        $result = $this->UserService->RegisterUser($request->validated());

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => new UserResource($result['user']),
        ], 201);

    }
}
