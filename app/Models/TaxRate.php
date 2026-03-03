<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxRate extends Model
{
    protected $fillable = [
        'tax_type_id',
        'harmonization_code',
        'rate_percent',
        'state',
    ];

    protected $casts = [
        'rate_percent' => 'decimal:2',
    ];

    public function taxType(): BelongsTo
    {
        return $this->belongsTo(TaxType::class);
    }

    public function taxPolicies(): BelongsToMany
    {
        return $this->belongsToMany(TaxPolicy::class, 'tax_rate_policies')
            ->withPivot('start_date', 'end_date')
            ->withTimestamps();
    }

    public function productTaxRates(): HasMany
    {
        return $this->hasMany(ProductTaxRate::class);
    }
}