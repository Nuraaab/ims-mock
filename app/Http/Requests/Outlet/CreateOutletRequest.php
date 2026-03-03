<?php

namespace App\Http\Requests\Outlet;

use Illuminate\Foundation\Http\FormRequest;

class CreateOutletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'outlet_type' => ['required', 'string', 'max:100'],
            'woreda_id' => ['nullable', 'integer', 'exists:woredas,id'],
            'kebele_id' => ['nullable', 'integer', 'exists:kebeles,id'],
        ];
    }
}
