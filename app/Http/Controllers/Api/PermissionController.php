<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Permission\PermissionResource;
use App\Models\AppPermission;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        $permissions = AppPermission::query()
            ->select(['id', 'key', 'value'])
            ->orderBy('key')
            ->get();

        return response()->json([
            'permissions' => PermissionResource::collection($permissions)->resolve(),
        ]);
    }
}
