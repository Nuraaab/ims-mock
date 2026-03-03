<?php

namespace App\Services\Role;
use App\Models\AppRole;

class RoleService
{
    public function CreateRole(array $payload) : array {

        $role = AppRole::create([
            'name' => $payload['name'],
        ]);

        

        return [
            'role' => $role,
        ];

    }
}
