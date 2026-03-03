<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Measurement extends Model
{
    protected $fillable = ['name', 'name_plural', 'symbol'];

    public function conversionsFrom(): HasMany
    {
        return $this->hasMany(MeasurementConversion::class, 'from_measurement_id');
    }

    public function conversionsTo(): HasMany
    {
        return $this->hasMany(MeasurementConversion::class, 'to_measurement_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'default_measurement_id');
    }

    public function productMeasurements(): HasMany
    {
        return $this->hasMany(ProductMeasurement::class);
    }

    public function priceListItems(): HasMany
    {
        return $this->hasMany(PriceListItem::class);
    }

    public function stockInItems(): HasMany
    {
        return $this->hasMany(StockInItem::class);
    }

    public function stockOutItems(): HasMany
    {
        return $this->hasMany(StockOutItem::class);
    }

    public function salesInvoiceItems(): HasMany
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }
}