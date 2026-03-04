<?php

namespace Modules\IMS\Http\Controllers;

use App\Services\Auth\PermissionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\IMS\Services\Measurment\MeasurmentService;

class MeasurmentController extends Controller
{
    public function __construct(
        private readonly MeasurmentService $measurmentService,
        private readonly PermissionService $permissionService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'measurements.view');

        $perPage = max(1, min(100, (int) $request->integer('per_page', 15)));
        $measurements = $this->measurmentService->paginate($perPage);

        return response()->json([
            'measurements' => $measurements->items(),
            'meta' => [
                'current_page' => $measurements->currentPage(),
                'last_page' => $measurements->lastPage(),
                'per_page' => $measurements->perPage(),
                'total' => $measurements->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'measurements.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:measurements,name'],
            'name_plural' => ['nullable', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:50', 'unique:measurements,symbol'],
        ]);

        $measurement = $this->measurmentService->create($validated);

        return response()->json([
            'message' => 'Measurement created successfully.',
            'measurement' => $measurement,
        ], 201);
    }

    public function show(Request $request, int $measurement): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'measurements.view');
        $record = $this->measurmentService->find($measurement);

        return response()->json([
            'measurement' => $record,
        ]);
    }

    public function update(Request $request, int $measurement): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'measurements.update');

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('measurements', 'name')->ignore($measurement)],
            'name_plural' => ['sometimes', 'nullable', 'string', 'max:255'],
            'symbol' => ['sometimes', 'nullable', 'string', 'max:50', Rule::unique('measurements', 'symbol')->ignore($measurement)],
        ]);

        $record = $this->measurmentService->update($measurement, $validated);

        return response()->json([
            'message' => 'Measurement updated successfully.',
            'measurement' => $record,
        ]);
    }

    public function destroy(Request $request, int $measurement): JsonResponse
    {
        $this->permissionService->authorize($request->user(), 'measurements.delete');

        $this->measurmentService->delete($measurement);

        return response()->json([
            'message' => 'Measurement deleted successfully.',
        ]);
    }
}
