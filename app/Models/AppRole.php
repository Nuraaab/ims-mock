<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppRole extends Model
{
    protected $table = 'roles';

    protected $fillable = ['name'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(AppPermission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    public function userBindings(): HasMany
    {
        return $this->hasMany(UserRoleBinding::class, 'role_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role_bindings', 'role_id', 'user_id')
            ->withPivot('scope', 'scope_id', 'include_descendents')
            ->withTimestamps();
    }

    public function permissionOverrides(): HasMany
    {
        return $this->hasMany(UserPermissionOverride::class, 'role_id');
    }
}