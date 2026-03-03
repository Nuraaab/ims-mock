<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = ['code', 'name', 'country_id'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    public function adminPivots(): HasMany
    {
        return $this->hasMany(AdminPivot::class, 'region_history_id');
    }
}