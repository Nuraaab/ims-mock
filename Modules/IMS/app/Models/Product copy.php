<?php

namespace Modules\IMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'item_id',
        'code',
        'barcode',
        'organization_id',
        'product_group_id',
        'default_measurement_id',
        'track_stock',
        'track_batch',
        'track_expiry',
    ];

    protected $casts = [
        'track_stock' => 'boolean',
        'track_batch' => 'boolean',
        'track_expiry' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function productGroup(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function defaultMeasurement(): BelongsTo
    {
        return $this->belongsTo(Measurement::class, 'default_measurement_id');
    }

    public function productMeasurements(): HasMany
    {
        return $this->hasMany(ProductMeasurement::class);
    }

    public function productTaxRates(): HasMany
    {
        return $this->hasMany(ProductTaxRate::class);
    }

    public function productBatches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function batchStockSummaries(): HasMany
    {
        return $this->hasMany(BatchStockSummary::class, 'product_batch_id');
    }

    public function stockSummaries(): HasMany
    {
        return $this->hasMany(StockSummary::class);
    }

    public function stockLedgers(): HasMany
    {
        return $this->hasMany(StockLedger::class);
    }

    public function stockInItems(): HasMany
    {
        return $this->hasMany(StockInItem::class);
    }

    public function stockOutItems(): HasMany
    {
        return $this->hasMany(StockOutItem::class);
    }

    public function stockTransferItems(): HasMany
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function stockAdjustmentItems(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }

    public function priceListItems(): HasMany
    {
        return $this->hasMany(PriceListItem::class);
    }

    public function salesInvoiceItems(): HasMany
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }
}
