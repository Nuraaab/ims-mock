<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'tin_number',
        'VAT_reg_number',
        'VAT_reg_date',
        'email',
        'phone',
        'house_number',
        'trade_name',
        'legal_name',
        'woreda_id',
        'kebele_id',
        'locality_id',
        'tax_center_id',
        'sector_type',
    ];

    protected $casts = [
        'VAT_reg_date' => 'date',
    ];

    public function woreda(): BelongsTo
    {
        return $this->belongsTo(Woreda::class);
    }

    public function kebele(): BelongsTo
    {
        return $this->belongsTo(Kebele::class);
    }

    public function locality(): BelongsTo
    {
        return $this->belongsTo(Locality::class);
    }

    public function taxCenter(): BelongsTo
    {
        return $this->belongsTo(TaxCenter::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function productGroups(): HasMany
    {
        return $this->hasMany(ProductGroup::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function loyaltyTiers(): HasMany
    {
        return $this->hasMany(LoyaltyTier::class);
    }

    public function loyaltyRules(): HasMany
    {
        return $this->hasMany(LoyaltyRule::class);
    }

    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrganizationHistory::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(OrganizationAddress::class, 'organization_history_id', 'id');
    }
}