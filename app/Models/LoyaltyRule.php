<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyRule extends Model
{
    protected $fillable = [
        'organization_id',
        'amount_base',
        'points_per_amount',
        'is_active',
    ];

    protected $casts = [
        'amount_base' => 'decimal:2',
        'points_per_amount' => 'integer',
        'is_active' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}