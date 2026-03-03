<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Role\RoleRequest;
use App\Http\Resources\Role\RoleResource;
use App\Services\Role\RoleService;

class RoleController extends Controller
{
 public function __construct(private readonly RoleService $roleService)
    {
       
    }

    public function CreateRole(RoleRequest $request) {

        $result = $this->roleService->CreateRole($request->validated());

        return response()->json([
            'message' => 'Role created successfully.',
            'role' => new RoleResource($result['role']),
        ], 201);

    }
}
