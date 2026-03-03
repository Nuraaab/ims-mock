<?php

namespace App\Http\Resources\Branch;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'name' => $this->name,
            'sub_tin' => $this->sub_tin,
            'woreda_id' => $this->woreda_id,
            'kebele_id' => $this->kebele_id,
            'locality_id' => $this->locality_id,
            'tax_center_id' => $this->tax_center_id,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
