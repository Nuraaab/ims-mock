<?php

namespace App\Services\User;

use App\Models\AppRole;
use App\Models\UserRoleBinding;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\IMS\Models\Branch;
use Modules\IMS\Models\Organization;
use Modules\IMS\Models\Outlet;
use Modules\IMS\Models\Warehouse;

class UserService
{
    public function registerUser(array $payload): array
    {
        $user = User::create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone' => $payload['phone'] ?? null,
            'national_id' => $payload['national_id'] ?? null,
            'password' => $payload['password'],
        ]);

        return [
            'user' => $user,
        ];
    }

    public function createStaff(User $actor, array $payload): array
    {
        return DB::transaction(function () use ($actor, $payload) {
            $organizationId = $this->resolveOrganizationIdForActor(
                $actor,
                $payload['organization_id'] ?? null
            );

            $scope = $payload['scope'];
            $scopeId = $payload['scope_id'] ?? null;

            if ($scope === 'organization') {
                $scopeId = $scopeId ?? $organizationId;
            }

            $this->assertScopeBelongsToOrganization($scope, $scopeId, $organizationId);

            $role = AppRole::query()->findOrFail($payload['role_id']);

            $user = User::create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'phone' => $payload['phone'] ?? null,
                'national_id' => $payload['national_id'] ?? null,
                'password' => $payload['password'],
            ]);

            $binding = UserRoleBinding::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'scope' => $scope,
                'scope_id' => $scopeId,
                'include_descendents' => $payload['include_descendents'] ?? false,
            ]);

            return [
                'user' => $user,
                'binding' => $binding,
                'role' => $role,
                'organization_id' => $organizationId,
            ];
        });
    }

    public function getStaffLookups(User $actor, ?int $organizationId = null): array
    {
        $resolvedOrganizationId = $this->resolveOrganizationIdForActor($actor, $organizationId);

        return [
            'organization_id' => $resolvedOrganizationId,
            'scopes' => ['organization', 'branch', 'warehouse', 'outlet'],
            'roles' => AppRole::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
            'branches' => Branch::query()
                ->select(['id', 'name'])
                ->where('organization_id', $resolvedOrganizationId)
                ->orderBy('name')
                ->get(),
            'warehouses' => Warehouse::query()
                ->select(['id', 'name', 'branch_id'])
                ->where('organization_id', $resolvedOrganizationId)
                ->orderBy('name')
                ->get(),
            'outlets' => Outlet::query()
                ->select(['outlets.id', 'outlets.name', 'outlets.branch_id'])
                ->whereHas('branch', function ($query) use ($resolvedOrganizationId) {
                    $query->where('organization_id', $resolvedOrganizationId);
                })
                ->orderBy('outlets.name')
                ->get(),
        ];
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

    private function assertScopeBelongsToOrganization(string $scope, ?int $scopeId, int $organizationId): void
    {
        if ($scopeId === null) {
            throw ValidationException::withMessages([
                'scope_id' => ['A scope_id is required for the selected scope.'],
            ]);
        }

        if ($scope === 'organization') {
            $exists = Organization::query()
                ->where('id', $scopeId)
                ->whereKey($organizationId)
                ->exists();

            if (! $exists) {
                throw ValidationException::withMessages([
                    'scope_id' => ['The organization scope is invalid.'],
                ]);
            }

            return;
        }

        if ($scope === 'branch') {
            $exists = Branch::query()
                ->where('id', $scopeId)
                ->where('organization_id', $organizationId)
                ->exists();

            if (! $exists) {
                throw ValidationException::withMessages([
                    'scope_id' => ['The branch does not belong to your organization.'],
                ]);
            }

            return;
        }

        if ($scope === 'warehouse') {
            $exists = Warehouse::query()
                ->where('id', $scopeId)
                ->where('organization_id', $organizationId)
                ->exists();

            if (! $exists) {
                throw ValidationException::withMessages([
                    'scope_id' => ['The warehouse does not belong to your organization.'],
                ]);
            }

            return;
        }

        if ($scope === 'outlet') {
            $exists = Outlet::query()
                ->where('id', $scopeId)
                ->whereHas('branch', function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                })
                ->exists();

            if (! $exists) {
                throw ValidationException::withMessages([
                    'scope_id' => ['The outlet does not belong to your organization.'],
                ]);
            }
        }
    }
}
