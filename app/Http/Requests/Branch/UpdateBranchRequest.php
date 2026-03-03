<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'sub_tin' => ['sometimes', 'nullable', 'string', 'max:100'],
            'woreda_id' => ['sometimes', 'nullable', 'integer', 'exists:woredas,id'],
            'kebele_id' => ['sometimes', 'nullable', 'integer', 'exists:kebeles,id'],
            'locality_id' => ['sometimes', 'nullable', 'integer', 'exists:localities,id'],
            'tax_center_id' => ['sometimes', 'nullable', 'integer', 'exists:tax_centers,id'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
        ];
    }
}
