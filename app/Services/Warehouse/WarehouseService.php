<?php

namespace App\Services\Warehouse;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Modules\IMS\Models\Branch;
use Modules\IMS\Models\Warehouse;

class WarehouseService
{
    public function index(User $actor, ?int $organizationId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Warehouse::query();

        if ($organizationId !== null) {
            $query->where('organization_id', $organizationId);
        }

        $accessibleWarehouseIds = require_scope_ids($actor, 'warehouses.view', 'warehouse');

        return $query
            ->whereIn('id', $accessibleWarehouseIds)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function store(User $actor, array $payload): Warehouse
    {
        $branchId = $payload['branch_id'] ?? null;
        $organizationId = $payload['organization_id'] ?? null;

        if ($branchId !== null) {
            $branch = Branch::query()->findOrFail((int) $branchId);
            $organizationId = (int) $branch->organization_id;
        } elseif ($organizationId === null) {
            $organizationId = required_payload_int(
                $payload,
                'organization_id',
                'organization_id is required when branch_id is not provided.'
            );
        }

        return Warehouse::create([
            'organization_id' => $organizationId,
            'branch_id' => $branchId,
            'name' => $payload['name'],
            'warehouse_type' => $payload['warehouse_type'],
            'woreda_id' => $payload['woreda_id'] ?? null,
            'kebele_id' => $payload['kebele_id'] ?? null,
        ]);
    }

    public function show(User $actor, int $warehouseId): Warehouse
    {
        return Warehouse::query()->findOrFail($warehouseId);
    }

    public function update(User $actor, int $warehouseId, array $payload): Warehouse
    {
        $warehouse = Warehouse::query()->findOrFail($warehouseId);
        $organizationId = (int) $warehouse->organization_id;

        if (array_key_exists('branch_id', $payload)) {
            if ($payload['branch_id'] !== null) {
                $this->assertBranchBelongsToOrganization((int) $payload['branch_id'], $organizationId);
            }
            $warehouse->branch_id = $payload['branch_id'];
        }
        if (array_key_exists('name', $payload)) {
            $warehouse->name = $payload['name'];
        }
        if (array_key_exists('warehouse_type', $payload)) {
            $warehouse->warehouse_type = $payload['warehouse_type'];
        }
        if (array_key_exists('woreda_id', $payload)) {
            $warehouse->woreda_id = $payload['woreda_id'];
        }
        if (array_key_exists('kebele_id', $payload)) {
            $warehouse->kebele_id = $payload['kebele_id'];
        }

        $warehouse->save();

        return $warehouse;
    }

    public function destroy(User $actor, int $warehouseId): void
    {
        $warehouse = Warehouse::query()->findOrFail($warehouseId);
        $warehouse->delete();
    }

    private function assertBranchBelongsToOrganization(int $branchId, int $organizationId): void
    {
        $belongs = Branch::query()
            ->where('id', $branchId)
            ->where('organization_id', $organizationId)
            ->exists();

        if (! $belongs) {
            throw ValidationException::withMessages([
                'branch_id' => ['The selected branch does not belong to this organization.'],
            ]);
        }
    }
}
