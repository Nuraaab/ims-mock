<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceListItem extends Model
{
    protected $fillable = [
        'price_list_id',
        'product_id',
        'measurement_id',
        'min_quantity',
        'max_quantity',
        'pricing_type',
        'value',
    ];

    protected $casts = [
        'min_quantity' => 'decimal:2',
        'max_quantity' => 'decimal:2',
        'value' => 'decimal:2',
    ];

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function measurement(): BelongsTo
    {
        return $this->belongsTo(Measurement::class);
    }
}