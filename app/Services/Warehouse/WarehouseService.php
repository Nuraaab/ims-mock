<?php

namespace App\Services\Warehouse;

use App\Models\User;
use App\Models\UserRoleBinding;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Modules\IMS\Models\Branch;
use Modules\IMS\Models\Warehouse;

class WarehouseService
{
    public function index(User $actor, ?int $organizationId = null, int $perPage = 15): LengthAwarePaginator
    {
        $resolvedOrganizationId = $this->resolveOrganizationIdForActor($actor, $organizationId);

        return Warehouse::query()
            ->where('organization_id', $resolvedOrganizationId)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function store(User $actor, array $payload): Warehouse
    {
        $organizationId = $this->resolveOrganizationIdForActor($actor, $payload['organization_id'] ?? null);
        $branchId = $payload['branch_id'] ?? null;

        if ($branchId !== null) {
            $this->assertBranchBelongsToOrganization((int) $branchId, $organizationId);
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
        $warehouse = Warehouse::query()->findOrFail($warehouseId);
        $this->assertActorCanAccessOrganization($actor, (int) $warehouse->organization_id);

        return $warehouse;
    }

    public function update(User $actor, int $warehouseId, array $payload): Warehouse
    {
        $warehouse = Warehouse::query()->findOrFail($warehouseId);
        $organizationId = (int) $warehouse->organization_id;
        $this->assertActorCanAccessOrganization($actor, $organizationId);

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
        $this->assertActorCanAccessOrganization($actor, (int) $warehouse->organization_id);
        $warehouse->delete();
    }

    private function resolveOrganizationIdForActor(User $actor, ?int $organizationId = null): int
    {
        $organizationBindings = UserRoleBinding::query()
            ->where('user_id', $actor->id)
            ->where('scope', 'organization')
            ->whereNotNull('scope_id');

        if ($organizationId !== null) {
            $hasAccess = (clone $organizationBindings)
                ->where('scope_id', $organizationId)
                ->exists();

            if (! $hasAccess) {
                throw ValidationException::withMessages([
                    'organization_id' => ['You do not have access to the selected organization.'],
                ]);
            }

            return $organizationId;
        }

        $resolved = (clone $organizationBindings)->value('scope_id');

        if (! $resolved) {
            throw ValidationException::withMessages([
                'organization_id' => ['No organization scope found for the current user.'],
            ]);
        }

        return (int) $resolved;
    }

    private function assertActorCanAccessOrganization(User $actor, int $organizationId): void
    {
        $hasAccess = UserRoleBinding::query()
            ->where('user_id', $actor->id)
            ->where('scope', 'organization')
            ->where('scope_id', $organizationId)
            ->exists();

        if (! $hasAccess) {
            throw ValidationException::withMessages([
                'organization_id' => ['You do not have access to this organization resource.'],
            ]);
        }
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
