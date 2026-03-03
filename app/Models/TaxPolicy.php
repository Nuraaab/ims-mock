<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaxPolicy extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function taxRates(): BelongsToMany
    {
        return $this->belongsToMany(TaxRate::class, 'tax_rate_policies')
            ->withPivot('start_date', 'end_date')
            ->withTimestamps();
    }

    public function withholdingRules(): BelongsToMany
    {
        return $this->belongsToMany(WithholdingRule::class, 'withholding_policies')
            ->withPivot('start_date', 'end_date')
            ->withTimestamps();
    }
}
