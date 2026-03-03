<?php

namespace Modules\IMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationHistory extends Model
{
    protected $fillable = [
        'organization_id',
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

    public function addresses(): HasMany
    {
        return $this->hasMany(OrganizationAddress::class);
    }
}
