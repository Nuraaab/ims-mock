<?php

namespace App\Services\Branch;

use App\Models\User;
use App\Models\UserRoleBinding;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Modules\IMS\Models\Branch;

class BranchService
{
    public function index(User $actor, ?int $organizationId = null, int $perPage = 15): LengthAwarePaginator
    {
        $resolvedOrganizationId = $this->resolveOrganizationIdForActor($actor, $organizationId);

        return Branch::query()
            ->where('organization_id', $resolvedOrganizationId)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function store(User $actor, array $payload): Branch
    {
        $organizationId = $this->resolveOrganizationIdForActor($actor, $payload['organization_id'] ?? null);

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
        $branch = Branch::query()->findOrFail($branchId);
        $this->assertActorCanAccessOrganization($actor, (int) $branch->organization_id);

        return $branch;
    }

    public function update(User $actor, int $branchId, array $payload): Branch
    {
        $branch = Branch::query()->findOrFail($branchId);
        $this->assertActorCanAccessOrganization($actor, (int) $branch->organization_id);

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
        $this->assertActorCanAccessOrganization($actor, (int) $branch->organization_id);
        $branch->delete();
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
