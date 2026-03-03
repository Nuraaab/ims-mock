<?php

namespace Modules\IMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'sub_tin',
        'woreda_id',
        'kebele_id',
        'locality_id',
        'tax_center_id',
        'email',
        'phone',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

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

    public function outlets(): HasMany
    {
        return $this->hasMany(Outlet::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function productGroups(): HasMany
    {
        return $this->hasMany(ProductGroup::class);
    }

    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }

    public function salesInvoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class, 'branch_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(BranchHistory::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(BranchAddress::class, 'branch_history_id', 'id');
    }
}
