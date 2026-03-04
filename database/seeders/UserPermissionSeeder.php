<?php

namespace Database\Seeders;

use App\Models\AppPermission;
use App\Models\AppRole;
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

            ['key' => 'branches.view', 'value' => 'View branches'],
            ['key' => 'branches.create', 'value' => 'Create branches'],
            ['key' => 'branches.update', 'value' => 'Update branches'],
            ['key' => 'branches.delete', 'value' => 'Delete branches'],

            ['key' => 'warehouses.view', 'value' => 'View warehouses'],
            ['key' => 'warehouses.create', 'value' => 'Create warehouses'],
            ['key' => 'warehouses.update', 'value' => 'Update warehouses'],
            ['key' => 'warehouses.delete', 'value' => 'Delete warehouses'],

            ['key' => 'outlets.view', 'value' => 'View outlets'],
            ['key' => 'outlets.create', 'value' => 'Create outlets'],
            ['key' => 'outlets.update', 'value' => 'Update outlets'],
            ['key' => 'outlets.delete', 'value' => 'Delete outlets'],

            ['key' => 'product-groups.view', 'value' => 'View product groups'],
            ['key' => 'product-groups.create', 'value' => 'Create product groups'],
            ['key' => 'product-groups.update', 'value' => 'Update product groups'],
            ['key' => 'product-groups.delete', 'value' => 'Delete product groups'],
            ['key' => 'item_categories.view', 'value' => 'View item categories'],
            ['key' => 'item_categories.create', 'value' => 'Create item categories'],
            ['key' => 'item_categories.update', 'value' => 'Update item categories'],
            ['key' => 'item_categories.delete', 'value' => 'Delete item categories'],
            ['key' => 'measurements.view', 'value' => 'View measurements'],
            ['key' => 'measurements.create', 'value' => 'Create measurements'],
            ['key' => 'measurements.update', 'value' => 'Update measurements'],
            ['key' => 'measurements.delete', 'value' => 'Delete measurements'],
        ];

        foreach ($permissions as $permission) {
            AppPermission::updateOrCreate(
                ['key' => $permission['key']],
                ['value' => $permission['value']]
            );
        }

        $superOwnerRole = AppRole::firstOrCreate(['name' => 'Super Owner']);
        $superOwnerRole->permissions()->sync(
            AppPermission::query()->pluck('id')->all()
        );
    }
}
