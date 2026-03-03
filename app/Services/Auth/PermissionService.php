<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserRoleBinding;
use Illuminate\Auth\Access\AuthorizationException;

class PermissionService
{
    public function authorize(User $user, string $permissionKey, ?int $organizationId = null): void
    {
        if (! $this->hasPermission($user, $permissionKey, $organizationId)) {
            throw new AuthorizationException('You are not authorized to perform this action.');
        }
    }

    public function hasPermission(User $user, string $permissionKey, ?int $organizationId = null): bool
    {
        $query = UserRoleBinding::query()
            ->where('user_id', $user->id)
            ->where('scope', 'organization')
            ->whereHas('role.permissions', function ($permissionQuery) use ($permissionKey) {
                $permissionQuery->where('key', $permissionKey);
            });

        if ($organizationId !== null) {
            $query->where('scope_id', $organizationId);
        }

        return $query->exists();
    }
}
