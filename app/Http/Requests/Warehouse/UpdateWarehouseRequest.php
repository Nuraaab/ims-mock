<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['sometimes', 'nullable', 'integer', 'exists:branches,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'warehouse_type' => ['sometimes', 'required', 'string', 'max:100'],
            'woreda_id' => ['sometimes', 'nullable', 'integer', 'exists:woredas,id'],
            'kebele_id' => ['sometimes', 'nullable', 'integer', 'exists:kebeles,id'],
        ];
    }
}
