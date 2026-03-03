<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\OutletController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WarehouseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::get('/registration-lookups', [AuthController::class, 'registrationLookups']);
    Route::post('/register-organization', [AuthController::class, 'registerOrganization']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/roles', [RoleController::class, 'CreateRole']);
    Route::put('/roles/{role}', [RoleController::class, 'UpdateRole']);
    Route::delete('/roles/{role}', [RoleController::class, 'DeleteRole']);
    Route::get('/users/staff', [UserController::class, 'listStaff']);
    Route::post('/users/staff', [UserController::class, 'createStaff']);
    Route::put('/users/staff/{user}', [UserController::class, 'updateStaff']);
    Route::delete('/users/staff/{user}', [UserController::class, 'deleteStaff']);
    Route::get('/users/staff/lookups', [UserController::class, 'staffLookups']);
    Route::get('/roles', [RoleController::class, 'GetRoles']);
    Route::get('/permissions', [PermissionController::class, 'index']);

    Route::apiResource('branches', BranchController::class);
    Route::apiResource('warehouses', WarehouseController::class);
    Route::apiResource('outlets', OutletController::class);
});
