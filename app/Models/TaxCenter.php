<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxCenter extends Model
{
    protected $fillable = [
        'name',
        'code',
        'administrative_level',
        'woreda_id',
    ];

    public function woreda(): BelongsTo
    {
        return $this->belongsTo(Woreda::class);
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TaxCenterHistory::class);
    }
}