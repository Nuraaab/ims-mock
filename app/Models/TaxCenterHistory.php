<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxCenterHistory extends Model
{
    protected $fillable = ['tax_center_id', 'history_data'];

    protected $casts = [
        'history_data' => 'array',
    ];

    public function taxCenter(): BelongsTo
    {
        return $this->belongsTo(TaxCenter::class);
    }

    public function organizationAddresses(): HasMany
    {
        return $this->hasMany(OrganizationAddress::class, 'tax_center_history_id');
    }

    public function branchAddresses(): HasMany
    {
        return $this->hasMany(BranchAddress::class, 'tax_center_history_id');
    }
}