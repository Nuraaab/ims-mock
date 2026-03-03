<?php

namespace Database\Seeders;

use App\Models\AppPermission;
use Illuminate\Database\Seeder;

class UserPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['key' => 'users.view', 'value' => 'View users'],
            ['key' => 'users.create', 'value' => 'Create users'],
            ['key' => 'users.update', 'value' => 'Update users'],
            ['key' => 'users.delete', 'value' => 'Delete users'],
            ['key' => 'roles.view', 'value' => 'View roles'],
            ['key' => 'roles.create', 'value' => 'Create roles'],
            ['key' => 'roles.update', 'value' => 'Update roles'],
            ['key' => 'roles.delete', 'value' => 'Delete roles'],
            ['key' => 'permissions.view', 'value' => 'View permissions'],
        ];

        foreach ($permissions as $permission) {
            AppPermission::updateOrCreate(
                ['key' => $permission['key']],
                ['value' => $permission['value']]
            );
        }
    }
}
