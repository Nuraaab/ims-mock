<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tin_number' => $this->tin_number,
            'email' => $this->email,
            'phone' => $this->phone,
            'trade_name' => $this->trade_name,
            'legal_name' => $this->legal_name,
            'created_at' => $this->created_at,
        ];
    }
}

