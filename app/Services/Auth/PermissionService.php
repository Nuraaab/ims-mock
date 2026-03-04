<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserRoleBinding;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\IMS\Models\Branch;
use Modules\IMS\Models\Outlet;
use Modules\IMS\Models\Warehouse;

class PermissionService
{
    public function authorize(
        User $user,
        string $permissionKey,
        ?int $organizationId = null,
        ?string $scope = null,
        ?int $scopeId = null
    ): void
    {
        if (! $this->hasPermission($user, $permissionKey, $organizationId, $scope, $scopeId)) {
            throw new AuthorizationException('You are not authorized to perform this action.');
        }
    }

    public function hasPermission(
        User $user,
        string $permissionKey,
        ?int $organizationId = null,
        ?string $scope = null,
        ?int $scopeId = null
    ): bool
    {
        $targetScope = $scope;
        $targetScopeId = $scopeId;

        if ($targetScope === null && $organizationId !== null) {
            $targetScope = 'organization';
            $targetScopeId = $organizationId;
        }

        $bindings = UserRoleBinding::query()
            ->where('user_id', $user->id)
            ->with(['descendants'])
            ->whereHas('role.permissions', function ($permissionQuery) use ($permissionKey) {
                $permissionQuery->where('key', $permissionKey);
            })
            ->get();

        if ($targetScope === null || $targetScopeId === null) {
            return $bindings->isNotEmpty();
        }

        return $bindings->contains(function (UserRoleBinding $binding) use ($targetScope, $targetScopeId) {
            return $this->bindingGrantsScope($binding, $targetScope, $targetScopeId);
        });
    }

    public function getAccessibleScopeIds(User $user, string $permissionKey, string $targetScope): array
    {
        $bindings = UserRoleBinding::query()
            ->where('user_id', $user->id)
            ->with(['descendants'])
            ->whereHas('role.permissions', function ($permissionQuery) use ($permissionKey) {
                $permissionQuery->where('key', $permissionKey);
            })
            ->get();

        if ($bindings->isEmpty()) {
            return [];
        }

        $ids = [];

        foreach ($bindings as $binding) {
            $ids = array_merge($ids, $this->expandScopeIdsFromBinding($binding, $targetScope));
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }

    private function bindingGrantsScope(UserRoleBinding $binding, string $targetScope, int $targetScopeId): bool
    {
        if ($binding->scope === $targetScope && (int) $binding->scope_id === $targetScopeId) {
            return true;
        }

        if ($binding->descendants->contains(function ($descendant) use ($targetScope, $targetScopeId) {
            return $descendant->scope === $targetScope && (int) $descendant->scope_id === $targetScopeId;
        })) {
            return true;
        }

        if (! $binding->include_descendents) {
            return false;
        }

        return in_array($targetScopeId, $this->expandScopeIdsFromBinding($binding, $targetScope), true);
    }

    private function expandScopeIdsFromBinding(UserRoleBinding $binding, string $targetScope): array
    {
        if (! $binding->scope_id) {
            return [];
        }

        $bindingScope = $binding->scope;
        $bindingScopeId = (int) $binding->scope_id;

        if ($bindingScope === $targetScope) {
            return [$bindingScopeId];
        }

        if (! $binding->include_descendents) {
            return [];
        }

        $fromDescendants = $binding->descendants
            ->where('scope', $targetScope)
            ->pluck('scope_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (! empty($fromDescendants)) {
            return $fromDescendants;
        }

        if ($bindingScope === 'organization') {
            return $this->expandFromOrganization($bindingScopeId, $targetScope);
        }

        if ($bindingScope === 'branch') {
            return $this->expandFromBranch($bindingScopeId, $targetScope);
        }

        return [];
    }

    private function expandFromOrganization(int $organizationId, string $targetScope): array
    {
        if ($targetScope === 'organization') {
            return [$organizationId];
        }

        if ($targetScope === 'branch') {
            return Branch::query()
                ->where('organization_id', $organizationId)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if ($targetScope === 'warehouse') {
            return Warehouse::query()
                ->where('organization_id', $organizationId)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if ($targetScope === 'outlet') {
            return Outlet::query()
                ->whereHas('branch', function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                })
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return [];
    }

    private function expandFromBranch(int $branchId, string $targetScope): array
    {
        if ($targetScope === 'branch') {
            return [$branchId];
        }

        if ($targetScope === 'warehouse') {
            return Warehouse::query()
                ->where('branch_id', $branchId)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if ($targetScope === 'outlet') {
            return Outlet::query()
                ->where('branch_id', $branchId)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return [];
    }
}
