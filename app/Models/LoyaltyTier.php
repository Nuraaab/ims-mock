<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyTier extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'min_points',
        'max_points',
    ];

    protected $casts = [
        'min_points' => 'integer',
        'max_points' => 'integer',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function customerLoyalties(): HasMany
    {
        return $this->hasMany(CustomerLoyalty::class);
    }

    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }
}