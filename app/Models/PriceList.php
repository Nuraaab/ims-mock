<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceList extends Model
{
    protected $fillable = [
        'organization_id',
        'code',
        'name',
        'description',
        'currency_id',
        'loyalty_tier_id',
        'customer_id',
        'customer_type',
        'company_id',
        'branch_id',
        'start_date',
        'end_date',
        'priority',
        'is_active',
        'is_base',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'priority' => 'integer',
        'is_active' => 'boolean',
        'is_base' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function loyaltyTier(): BelongsTo
    {
        return $this->belongsTo(LoyaltyTier::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceListItem::class);
    }
}