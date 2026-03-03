<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Woreda extends Model
{
    protected $fillable = ['code', 'name', 'zone_id'];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function kebeles(): HasMany
    {
        return $this->hasMany(Kebele::class);
    }

    public function taxCenters(): HasMany
    {
        return $this->hasMany(TaxCenter::class);
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function outlets(): HasMany
    {
        return $this->hasMany(Outlet::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function adminPivots(): HasMany
    {
        return $this->hasMany(AdminPivot::class, 'woreda_history_id');
    }
}