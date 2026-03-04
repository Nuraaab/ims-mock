<?php
namespace Modules\IMS\Services\ProductGroup;

use App\Models\User;
use App\Models\UserRoleBinding;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Modules\IMS\Models\Branch;
use Modules\IMS\Models\ProductGroup;

class ProductGroupService
{
    public function index(User $actor, ?int $organizationId = null, int $perPage = 15): LengthAwarePaginator
    {
        $resolvedOrganizationId = $this->resolveOrganizationIdForActor($actor, $organizationId);

        return ProductGroup::query()
            ->where('organization_id', $resolvedOrganizationId)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function store(User $actor, array $payload): ProductGroup
    {
        $organizationId = $this->resolveOrganizationIdForActor($actor, $payload['organization_id'] ?? null);
        $branchId = $payload['branch_id'] ?? null;

        if ($branchId !== null) {
            $this->assertBranchBelongsToOrganization((int) $branchId, $organizationId);
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
        $productGroup = ProductGroup::query()->findOrFail($productGroupId);
        $this->assertActorCanAccessOrganization($actor, (int) $productGroup->organization_id);

        return $productGroup;
    }

    public function update(User $actor, int $productGroupId, array $payload): ProductGroup
    {
        $productGroup = ProductGroup::query()->findOrFail($productGroupId);
        $organizationId = (int) $productGroup->organization_id;
        $this->assertActorCanAccessOrganization($actor, $organizationId);

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
        $this->assertActorCanAccessOrganization($actor, (int) $productGroup->organization_id);
        $productGroup->delete();
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
