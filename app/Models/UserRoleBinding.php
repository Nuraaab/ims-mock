<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserRoleBinding extends Model
{
    protected $fillable = [
        'user_id',
        'role_id',
        'scope',
        'scope_id',
        'include_descendents',
    ];

    protected $casts = [
        'include_descendents' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(AppRole::class, 'role_id');
    }

    public function descendants(): HasMany
    {
        return $this->hasMany(UserRoleBindingDescendent::class);
    }
}