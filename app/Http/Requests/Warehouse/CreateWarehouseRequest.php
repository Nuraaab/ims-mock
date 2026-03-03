<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class CreateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'warehouse_type' => ['required', 'string', 'max:100'],
            'woreda_id' => ['nullable', 'integer', 'exists:woredas,id'],
            'kebele_id' => ['nullable', 'integer', 'exists:kebeles,id'],
        ];
    }
}
