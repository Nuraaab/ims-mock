<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationAddress extends Model
{
    protected $fillable = [
        'admin_pivot_id',
        'kebele_history_id',
        'locality_history_id',
        'tax_center_history_id',
        'organization_history_id',
    ];

    public function adminPivot(): BelongsTo
    {
        return $this->belongsTo(AdminPivot::class);
    }

    public function kebeleHistory(): BelongsTo
    {
        return $this->belongsTo(Kebele::class, 'kebele_history_id');
    }

    public function localityHistory(): BelongsTo
    {
        return $this->belongsTo(Locality::class, 'locality_history_id');
    }

    public function taxCenterHistory(): BelongsTo
    {
        return $this->belongsTo(TaxCenterHistory::class);
    }

    public function organizationHistory(): BelongsTo
    {
        return $this->belongsTo(OrganizationHistory::class);
    }
}