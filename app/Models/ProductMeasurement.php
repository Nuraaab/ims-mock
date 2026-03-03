<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMeasurement extends Model
{
    protected $fillable = [
        'product_id',
        'measurement_id',
        'conversion_rate',
        'unit_price',
        'is_base',
    ];

    protected $casts = [
        'conversion_rate' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'is_base' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function measurement(): BelongsTo
    {
        return $this->belongsTo(Measurement::class);
    }
}