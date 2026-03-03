<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['code', 'name', 'region_id'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function woredas(): HasMany
    {
        return $this->hasMany(Woreda::class);
    }

    public function adminPivots(): HasMany
    {
        return $this->hasMany(AdminPivot::class, 'zone_history_id');
    }
}