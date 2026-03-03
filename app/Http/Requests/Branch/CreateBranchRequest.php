<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class CreateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'sub_tin' => ['nullable', 'string', 'max:100'],
            'woreda_id' => ['nullable', 'integer', 'exists:woredas,id'],
            'kebele_id' => ['nullable', 'integer', 'exists:kebeles,id'],
            'locality_id' => ['nullable', 'integer', 'exists:localities,id'],
            'tax_center_id' => ['nullable', 'integer', 'exists:tax_centers,id'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}
