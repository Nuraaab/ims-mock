<?php
namespace Modules\IMS\Services\ProductGroup;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Modules\IMS\Models\Branch;
use Modules\IMS\Models\ProductGroup;

class ProductGroupService
{
    public function index(User $actor, ?int $organizationId = null, int $perPage = 15): LengthAwarePaginator
    {
        $organizationIds = scope_ids($actor, 'product-groups.view', 'organization');
        $branchIds = scope_ids($actor, 'product-groups.view', 'branch');

        if (empty($organizationIds) && empty($branchIds)) {
            throw ValidationException::withMessages([
                'organization_id' => ['No accessible product group scope found for the current user.'],
            ]);
        }

        return ProductGroup::query()
            ->where(function ($query) use ($organizationIds, $branchIds) {
                if (! empty($organizationIds)) {
                    $query->orWhereIn('organization_id', $organizationIds);
                }
                if (! empty($branchIds)) {
                    $query->orWhereIn('branch_id', $branchIds);
                }
            })
            ->when($organizationId !== null, function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function store(User $actor, array $payload): ProductGroup
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

        return ProductGroup::create([
            'organization_id' => $organizationId,
            'branch_id' => $branchId,
            'name' => $payload['name'],
            'description' => $payload['description'] ?? null,
        ]);
    }

    public function show(User $actor, int $productGroupId): ProductGroup
    {
        return ProductGroup::query()->findOrFail($productGroupId);
    }

    public function update(User $actor, int $productGroupId, array $payload): ProductGroup
    {
        $productGroup = ProductGroup::query()->findOrFail($productGroupId);
        $organizationId = (int) $productGroup->organization_id;

        if (array_key_exists('branch_id', $payload)) {
            if ($payload['branch_id'] !== null) {
                $this->assertBranchBelongsToOrganization((int) $payload['branch_id'], $organizationId);
            }

            $productGroup->branch_id = $payload['branch_id'];
        }
        if (array_key_exists('name', $payload)) {
            $productGroup->name = $payload['name'];
        }
        if (array_key_exists('description', $payload)) {
            $productGroup->description = $payload['description'];
        }

        $productGroup->save();

        return $productGroup;
    }

    public function destroy(User $actor, int $productGroupId): void
    {
        $productGroup = ProductGroup::query()->findOrFail($productGroupId);
        $productGroup->delete();
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
