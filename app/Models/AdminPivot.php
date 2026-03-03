<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminPivot extends Model
{
    protected $fillable = [
        'woreda_history_id',
        'zone_history_id',
        'region_history_id',
    ];

    public function woredaHistory(): BelongsTo
    {
        return $this->belongsTo(Woreda::class, 'woreda_history_id');
    }

    public function zoneHistory(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_history_id');
    }

    public function regionHistory(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_history_id');
    }

    public function organizationAddresses(): HasMany
    {
        return $this->hasMany(OrganizationAddress::class);
    }

    public function branchAddresses(): HasMany
    {
        return $this->hasMany(BranchAddress::class);
    }
}