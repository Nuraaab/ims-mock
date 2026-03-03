<?php

namespace App\Services\Role;

use App\Models\AppRole;
use Illuminate\Validation\ValidationException;

class RoleService
{
    public function CreateRole(array $payload): array
    {
        $role = AppRole::create([
            'name' => $payload['name'],
        ]);

        $permissionIds = $payload['permission_ids'] ?? [];
        if (! empty($permissionIds)) {
            $role->permissions()->sync($permissionIds);
        }

        return [
            'role' => $role->load('permissions:id,key,value'),
        ];
    }

    public function getRoles()
    {
        return AppRole::query()
            ->with('permissions:id,key,value')
            ->orderBy('name')
            ->get();
    }

    public function updateRole(int $roleId, array $payload): array
    {
        $role = AppRole::query()->findOrFail($roleId);
        $this->assertRoleIsMutable($role);

        $role->update([
            'name' => $payload['name'],
        ]);

        $permissionIds = $payload['permission_ids'] ?? [];
        $role->permissions()->sync($permissionIds);

        return [
            'role' => $role->load('permissions:id,key,value'),
        ];
    }

    public function deleteRole(int $roleId): void
    {
        $role = AppRole::query()->findOrFail($roleId);
        $this->assertRoleIsMutable($role);
        $role->delete();
    }

    private function assertRoleIsMutable(AppRole $role): void
    {
        if (mb_strtolower(trim($role->name)) === 'super owner') {
            throw ValidationException::withMessages([
                'role' => ['Super Owner role cannot be edited or deleted.'],
            ]);
        }
    }
}
