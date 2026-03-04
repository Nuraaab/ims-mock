<?php

namespace App\Services\Branch;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\IMS\Models\Branch;

class BranchService
{
    public function index(User $actor, ?int $organizationId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Branch::query();

        if ($organizationId !== null) {
            $query->where('organization_id', $organizationId);
        }

        $accessibleBranchIds = require_scope_ids($actor, 'branches.view', 'branch');

        return $query
            ->whereIn('id', $accessibleBranchIds)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function store(User $actor, array $payload): Branch
    {
        $organizationId = required_payload_int($payload, 'organization_id', 'organization_id is required to create a branch.');

        return Branch::create([
            'organization_id' => $organizationId,
            'name' => $payload['name'],
            'sub_tin' => $payload['sub_tin'] ?? null,
            'woreda_id' => $payload['woreda_id'] ?? null,
            'kebele_id' => $payload['kebele_id'] ?? null,
            'locality_id' => $payload['locality_id'] ?? null,
            'tax_center_id' => $payload['tax_center_id'] ?? null,
            'email' => $payload['email'] ?? null,
            'phone' => $payload['phone'] ?? null,
        ]);
    }

    public function show(User $actor, int $branchId): Branch
    {
        return Branch::query()->findOrFail($branchId);
    }

    public function update(User $actor, int $branchId, array $payload): Branch
    {
        $branch = Branch::query()->findOrFail($branchId);

        if (array_key_exists('name', $payload)) {
            $branch->name = $payload['name'];
        }
        if (array_key_exists('sub_tin', $payload)) {
            $branch->sub_tin = $payload['sub_tin'];
        }
        if (array_key_exists('woreda_id', $payload)) {
            $branch->woreda_id = $payload['woreda_id'];
        }
        if (array_key_exists('kebele_id', $payload)) {
            $branch->kebele_id = $payload['kebele_id'];
        }
        if (array_key_exists('locality_id', $payload)) {
            $branch->locality_id = $payload['locality_id'];
        }
        if (array_key_exists('tax_center_id', $payload)) {
            $branch->tax_center_id = $payload['tax_center_id'];
        }
        if (array_key_exists('email', $payload)) {
            $branch->email = $payload['email'];
        }
        if (array_key_exists('phone', $payload)) {
            $branch->phone = $payload['phone'];
        }

        $branch->save();

        return $branch;
    }

    public function destroy(User $actor, int $branchId): void
    {
        $branch = Branch::query()->findOrFail($branchId);
        $branch->delete();
    }
}
