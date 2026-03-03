<?php

namespace Modules\IMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeasurementConversion extends Model
{
    protected $fillable = [
        'from_measurement_id',
        'to_measurement_id',
        'conversion_rate',
    ];

    protected $casts = [
        'conversion_rate' => 'decimal:4',
    ];

    public function fromMeasurement(): BelongsTo
    {
        return $this->belongsTo(Measurement::class, 'from_measurement_id');
    }

    public function toMeasurement(): BelongsTo
    {
        return $this->belongsTo(Measurement::class, 'to_measurement_id');
    }
}
