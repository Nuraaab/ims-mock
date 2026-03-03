<?php

namespace App\Http\Resources\Outlet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'name' => $this->name,
            'outlet_type' => $this->outlet_type,
            'woreda_id' => $this->woreda_id,
            'kebele_id' => $this->kebele_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
