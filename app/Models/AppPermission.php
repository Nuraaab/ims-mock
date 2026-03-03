<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AppPermission extends Model
{
    protected $table = 'permissions';

    protected $fillable = ['key', 'value'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AppRole::class, 'role_permissions', 'permission_id', 'role_id');
    }
}
