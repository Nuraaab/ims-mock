<?php

namespace App\Services\Outlet;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\IMS\Models\Branch;
use Modules\IMS\Models\Outlet;

class OutletService
{
    public function index(User $actor, ?int $organizationId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Outlet::query();

        if ($organizationId !== null) {
            $query->whereHas('branch', function ($branchQuery) use ($organizationId) {
                $branchQuery->where('organization_id', $organizationId);
            });
        }

        $accessibleOutletIds = require_scope_ids($actor, 'outlets.view', 'outlet');

        return $query
            ->whereIn('id', $accessibleOutletIds)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function store(User $actor, array $payload): Outlet
    {
        $branch = Branch::query()->findOrFail($payload['branch_id']);

        return Outlet::create([
            'branch_id' => $branch->id,
            'name' => $payload['name'],
            'outlet_type' => $payload['outlet_type'],
            'woreda_id' => $payload['woreda_id'] ?? null,
            'kebele_id' => $payload['kebele_id'] ?? null,
        ]);
    }

    public function show(User $actor, int $outletId): Outlet
    {
        return Outlet::query()->findOrFail($outletId);
    }

    public function update(User $actor, int $outletId, array $payload): Outlet
    {
        $outlet = Outlet::query()->findOrFail($outletId);

        if (array_key_exists('branch_id', $payload)) {
            $targetBranch = Branch::query()->findOrFail($payload['branch_id']);
            $outlet->branch_id = $targetBranch->id;
        }
        if (array_key_exists('name', $payload)) {
            $outlet->name = $payload['name'];
        }
        if (array_key_exists('outlet_type', $payload)) {
            $outlet->outlet_type = $payload['outlet_type'];
        }
        if (array_key_exists('woreda_id', $payload)) {
            $outlet->woreda_id = $payload['woreda_id'];
        }
        if (array_key_exists('kebele_id', $payload)) {
            $outlet->kebele_id = $payload['kebele_id'];
        }

        $outlet->save();

        return $outlet;
    }

    public function destroy(User $actor, int $outletId): void
    {
        $outlet = Outlet::query()->findOrFail($outletId);
        $outlet->delete();
    }
}
