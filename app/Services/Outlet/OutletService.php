<?php

namespace App\Services\Outlet;

use App\Models\User;
use App\Models\UserRoleBinding;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Modules\IMS\Models\Branch;
use Modules\IMS\Models\Outlet;

class OutletService
{
    public function index(User $actor, ?int $organizationId = null, int $perPage = 15): LengthAwarePaginator
    {
        $resolvedOrganizationId = $this->resolveOrganizationIdForActor($actor, $organizationId);

        return Outlet::query()
            ->whereHas('branch', function ($query) use ($resolvedOrganizationId) {
                $query->where('organization_id', $resolvedOrganizationId);
            })
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function store(User $actor, array $payload): Outlet
    {
        $branch = Branch::query()->findOrFail($payload['branch_id']);
        $this->assertActorCanAccessOrganization($actor, (int) $branch->organization_id);

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
        $outlet = Outlet::query()->findOrFail($outletId);
        $branch = Branch::query()->findOrFail($outlet->branch_id);
        $this->assertActorCanAccessOrganization($actor, (int) $branch->organization_id);

        return $outlet;
    }

    public function update(User $actor, int $outletId, array $payload): Outlet
    {
        $outlet = Outlet::query()->findOrFail($outletId);
        $currentBranch = Branch::query()->findOrFail($outlet->branch_id);
        $this->assertActorCanAccessOrganization($actor, (int) $currentBranch->organization_id);

        if (array_key_exists('branch_id', $payload)) {
            $targetBranch = Branch::query()->findOrFail($payload['branch_id']);
            $this->assertActorCanAccessOrganization($actor, (int) $targetBranch->organization_id);
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
        $branch = Branch::query()->findOrFail($outlet->branch_id);
        $this->assertActorCanAccessOrganization($actor, (int) $branch->organization_id);
        $outlet->delete();
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
}
