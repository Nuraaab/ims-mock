<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Locality extends Model
{
    protected $fillable = ['code', 'name', 'kebele_id'];

    public function kebele(): BelongsTo
    {
        return $this->belongsTo(Kebele::class);
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}