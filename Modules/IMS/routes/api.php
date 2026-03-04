<?php

use Illuminate\Support\Facades\Route;
use Modules\IMS\Http\Controllers\IMSController;
use Modules\IMS\Http\Controllers\ProductGroupController;
use Modules\IMS\Http\Controllers\ItemCategoryController;
use Modules\IMS\Http\Controllers\MeasurmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ims', IMSController::class)->names('ims');
    Route::apiResource('item-categories', ItemCategoryController::class)->names('item-categories');
    Route::apiResource('measurements', MeasurmentController::class)
        ->parameters(['measurements' => 'measurement'])
        ->names('measurements');
    Route::apiResource('product-groups', ProductGroupController::class)
        ->parameters(['product-groups' => 'productGroup'])
        ->names('product-groups');
});

