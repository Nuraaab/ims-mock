<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WithholdingRule extends Model
{
    protected $fillable = [
        'item_type',
        'minimum_holdable',
        'state',
    ];

    protected $casts = [
        'minimum_holdable' => 'decimal:2',
    ];

    public function taxPolicies(): BelongsToMany
    {
        return $this->belongsToMany(TaxPolicy::class, 'withholding_policies')
            ->withPivot('start_date', 'end_date')
            ->withTimestamps();
    }
}