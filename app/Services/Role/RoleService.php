<?php

namespace App\Services\Role;

use App\Models\AppRole;

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
}
