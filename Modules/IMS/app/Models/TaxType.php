<?php

namespace Modules\IMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxType extends Model
{
    protected $fillable = ['code', 'name', 'state'];

    public function taxRates(): HasMany
    {
        return $this->hasMany(TaxRate::class);
    }
}
