<?php

namespace Modules\IMS\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'item' => $this->whenLoaded('item', function () {
                return [
                    'id' => $this->item?->id,
                    'name' => $this->item?->name,
                    'item_type' => $this->item?->item_type,
                ];
            }),
            'organization_id' => $this->organization_id,
            'product_group_id' => $this->product_group_id,
            'product_group' => $this->whenLoaded('productGroup', function () {
                return [
                    'id' => $this->productGroup?->id,
                    'name' => $this->productGroup?->name,
                ];
            }),
            'default_measurement_id' => $this->default_measurement_id,
            'default_measurement' => $this->whenLoaded('defaultMeasurement', function () {
                return [
                    'id' => $this->defaultMeasurement?->id,
                    'name' => $this->defaultMeasurement?->name,
                    'symbol' => $this->defaultMeasurement?->symbol,
                ];
            }),
            'code' => $this->code,
            'barcode' => $this->barcode,
            'track_stock' => (bool) $this->track_stock,
            'track_batch' => (bool) $this->track_batch,
            'track_expiry' => (bool) $this->track_expiry,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
