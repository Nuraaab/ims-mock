<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPermissionOverride extends Model
{
    protected $fillable = [
        'user_id',
        'role_id',
        'include_descendents',
        'scope',
        'scope_id',
        'allow',
    ];

    protected $casts = [
        'include_descendents' => 'boolean',
        'allow' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(AppRole::class, 'role_id');
    }
}