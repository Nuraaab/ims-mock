<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRoleBindingDescendent extends Model
{
    protected $table = 'user_role_binding_descendents';
    
    public $timestamps = false;

    protected $fillable = [
        'user_role_binding_id',
        'scope',
        'scope_id',
    ];

    public function userRoleBinding(): BelongsTo
    {
        return $this->belongsTo(UserRoleBinding::class);
    }
}